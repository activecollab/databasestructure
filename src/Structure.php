<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\Builder\AssociationsBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionBuilder\BaseCollectionDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionBuilder\BaseTypeCollectionInterfaceBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionBuilder\TypeCollectionInterfaceBuilder;
use ActiveCollab\DatabaseStructure\Builder\EntityBuilder\BaseDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\ManagerBuilder\BaseManagerDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\EntityBuilder\BaseTypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionBuilder\BaseTypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\EntityBuilder\BaseTypeInterfaceBuilder;
use ActiveCollab\DatabaseStructure\Builder\ManagerBuilder\BaseTypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionBuilder\CollectionDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\DatabaseBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\ManagerBuilder\BaseTypeManagerInterfaceBuilder;
use ActiveCollab\DatabaseStructure\Builder\ManagerBuilder\ManagerDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\ManagerBuilder\TypeManagerInterfaceBuilder;
use ActiveCollab\DatabaseStructure\Builder\RecordsBuilder;
use ActiveCollab\DatabaseStructure\Builder\SqlDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\TriggersBuilder;
use ActiveCollab\DatabaseStructure\Builder\EntityBuilder\TypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionBuilder\TypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\EntityBuilder\TypeInterfaceBuilder;
use ActiveCollab\DatabaseStructure\Builder\ManagerBuilder\TypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypesBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Field\Composite\CreatedAtField;
use ActiveCollab\DatabaseStructure\Field\Composite\UpdatedAtField;
use InvalidArgumentException;
use ReflectionClass;

abstract class Structure implements StructureInterface
{
    /**
     * Construct a new instance.
     */
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Configure types, fields and associations.
     */
    abstract protected function configure(): void;

    /**
     * @var iterable|TypeInterface[]
     */
    private array $types = [];

    /**
     * {@inheritdoc}
     */
    public function getTypes(): iterable
    {
        return $this->types;
    }

    /**
     * {@internal }.
     */
    public function getType($type_name): TypeInterface
    {
        if (isset($this->types[$type_name])) {
            return $this->types[$type_name];
        }

        throw new InvalidArgumentException("Type '$type_name' not found");
    }

    /**
     * @param  string        $type_name
     * @return TypeInterface
     */
    protected function &addType(string $type_name): TypeInterface
    {
        if (empty($this->types[$type_name])) {
            $this->types[$type_name] = new Type($type_name);

            switch ($this->getConfig('add_permissions')) {
                case self::ADD_PERMISSIVE_PERMISSIONS:
                    $this->types[$type_name]->permissions();
                    break;
                case self::ADD_RESTRICTIVE_PERMISSIONS:
                    $this->types[$type_name]->permissions(true, false);
                    break;
            }

            if ($this->getConfig('base_class_extends')) {
                $this->types[$type_name]->setBaseClassExtends($this->getConfig('base_class_extends'));
            }

            return $this->types[$type_name];
        }

        throw new InvalidArgumentException("Type '$type_name' already added");
    }

    /**
     * @var RecordInterface[]
     */
    private array $records = [];

    /**
     * {@inheritdoc}
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * Add a record to the initial data set.
     *
     * @param  string             $type_name
     * @param  array              $record
     * @param  string             $comment
     * @return StructureInterface
     */
    protected function &addRecord(string $type_name, array $record, string $comment = ''): StructureInterface
    {
        $type = $this->getType($type_name);

        $has_created_at = false;
        $has_updated_at = false;

        foreach ($type->getFields() as $field) {
            if ($field instanceof CreatedAtField && !array_key_exists('created_at', $record)) {
                $has_created_at = true;
            } elseif ($field instanceof UpdatedAtField && !array_key_exists('updated_at', $record)) {
                $has_updated_at = true;
            }
        }

        return $this->addTableRecord($type->getTableName(), $record, $comment, $has_created_at, $has_updated_at);
    }

    /**
     * Add multiple records to the initial data set.
     *
     * @param  string             $type_name
     * @param  array              $field_names
     * @param  array              $records_to_add
     * @param  string             $comment
     * @return StructureInterface
     */
    protected function &addRecords(string $type_name, array $field_names, array $records_to_add, string $comment = ''): StructureInterface
    {
        $type = $this->getType($type_name);

        $has_created_at = false;
        $has_updated_at = false;

        foreach ($type->getFields() as $field) {
            if ($field instanceof CreatedAtField) {
                $has_created_at = true;
            } elseif ($field instanceof UpdatedAtField) {
                $has_updated_at = true;
            }
        }

        return $this->addTableRecords($type->getTableName(), $field_names, $records_to_add, $comment, $has_created_at, $has_updated_at);
    }

    /**
     * Add a record to the initial data set.
     */
    private function &addTableRecord(
        string $table_name,
        array $record,
        string $comment = '',
        bool
        $auto_set_created_at = false,
        bool $auto_set_updated_at = false,
    ): StructureInterface
    {
        $single_record = new SingleRecord($table_name, $record, $comment);

        if ($auto_set_created_at && !array_key_exists('created_at', $record)) {
            $single_record->autoSetCreatedAt();
        }

        if ($auto_set_updated_at && !array_key_exists('updated_at', $record)) {
            $single_record->autoSetUpdatedAt();
        }

        $this->records[] = $single_record;

        return $this;
    }

    /**
     * Add multiple records to the initial data set.
     */
    private function &addTableRecords(
        string $table_name,
        array $field_names,
        array $records_to_add,
        string $comment = '',
        bool $auto_set_created_at = false,
        bool $auto_set_updated_at = false,
    ): StructureInterface
    {
        $multi_record = new MultiRecord($table_name, $field_names, $records_to_add, $comment);

        if ($auto_set_created_at && !in_array('created_at', $field_names)) {
            $multi_record->autoSetCreatedAt();
        }

        if ($auto_set_updated_at && !in_array('updated_at', $field_names)) {
            $multi_record->autoSetUpdatedAt();
        }

        $this->records[] = $multi_record;

        return $this;
    }

    private array $config = [];

    public function getConfig(string $name, $default = null)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : $default;
    }

    public function &setConfig($name, $value): StructureInterface
    {
        $this->config[$name] = $value;

        return $this;
    }

    private ?string $namespace = null;

    public function getNamespace(): string
    {
        if ($this->namespace === null) {
            $this->namespace = (new ReflectionClass(get_class($this)))->getNamespaceName();
        }

        return $this->namespace;
    }

    public function setNamespace(?string $namespace): static
    {
        $this->namespace = $namespace;

        if ($this->namespace) {
            $this->namespace = trim($this->namespace, '\\');
        }

        return $this;
    }

    // ---------------------------------------------------
    //  Class Builder
    // ---------------------------------------------------

    public function build(
        string $build_path = null,
        ConnectionInterface $connection = null,
        array $event_handlers = []
    ): void
    {
        $builders = $this->getBuilders($build_path, $connection, $event_handlers);

        foreach ($builders as $builder) {
            $builder->preBuild();
        }

        foreach ($this->types as $type) {
            foreach ($builders as $builder) {
                $builder->buildType($type);
            }
        }

        foreach ($builders as $builder) {
            $builder->postBuild();
        }
    }

    /**
     * @var BuilderInterface[]|FileSystemBuilderInterface[]|DatabaseBuilderInterface[]
     */
    private array $builders = [];

    /**
     * Return a list of prepared builder instances.
     *
     * @return BuilderInterface[]
     */
    private function getBuilders(
        ?string $build_path,
        ?ConnectionInterface $connection,
        array $event_handlers
    ): array
    {
        if (empty($this->builders)) {
            $this->builders[] = new BaseDirBuilder($this);
            $this->builders[] = new SqlDirBuilder($this);

            $this->builders[] = new TypesBuilder($this);
            $this->builders[] = new BaseTypeInterfaceBuilder($this);
            $this->builders[] = new TypeInterfaceBuilder($this);
            $this->builders[] = new BaseTypeClassBuilder($this);
            $this->builders[] = new TypeClassBuilder($this);
            $this->builders[] = new TypeTableBuilder($this);

            $this->builders[] = new AssociationsBuilder($this);
            $this->builders[] = new TriggersBuilder($this);
            $this->builders[] = new RecordsBuilder($this);

            $this->builders[] = new ManagerDirBuilder($this);
            $this->builders[] = new BaseManagerDirBuilder($this);

            $this->builders[] = new BaseTypeManagerInterfaceBuilder($this);
            $this->builders[] = new TypeManagerInterfaceBuilder($this);
            $this->builders[] = new BaseTypeManagerBuilder($this);
            $this->builders[] = new TypeManagerBuilder($this);

            $this->builders[] = new CollectionDirBuilder($this);
            $this->builders[] = new BaseCollectionDirBuilder($this);

            $this->builders[] = new BaseTypeCollectionInterfaceBuilder($this);
            $this->builders[] = new TypeCollectionInterfaceBuilder($this);
            $this->builders[] = new BaseTypeCollectionBuilder($this);
            $this->builders[] = new TypeCollectionBuilder($this);

            if ($build_path) {
                foreach ($this->builders as $k => $v) {
                    if ($v instanceof FileSystemBuilderInterface) {
                        $this->builders[$k]->setBuildPath($build_path);
                    }
                }
            }

            if ($connection) {
                foreach ($this->builders as $k => $v) {
                    if ($v instanceof DatabaseBuilderInterface) {
                        $this->builders[$k]->setConnection($connection);
                    }
                }
            }

            foreach ($event_handlers as $event => $handler) {
                foreach ($this->builders as $v) {
                    $v->registerEventHandler($event, $handler);
                }
            }
        }

        return $this->builders;
    }
}
