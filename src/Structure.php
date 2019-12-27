<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\Builder\Database\AssociationsBuilder;
use ActiveCollab\DatabaseStructure\Builder\Directories\BaseCollectionDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Directories\BaseDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Directories\BaseManagerDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Entities\BaseTypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\Collection\BaseTypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\Entities\TypeDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Manager\BaseTypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\Directories\CollectionDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Database\DatabaseBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\Directories\ManagerDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Database\RecordsBuilder;
use ActiveCollab\DatabaseStructure\Builder\Directories\SqlDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\Database\TriggersBuilder;
use ActiveCollab\DatabaseStructure\Builder\Entities\TypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\Collection\TypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\Manager\TypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypesBuilder;
use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Field\Composite\CreatedAtField;
use ActiveCollab\DatabaseStructure\Field\Composite\UpdatedAtField;
use InvalidArgumentException;
use ReflectionClass;

abstract class Structure implements StructureInterface
{
    public function __construct()
    {
        $this->configure();
    }

    abstract protected function configure();

    /**
     * @var iterable|TypeInterface[]
     */
    private $types = [];

    /**
     * @return iterable|TypeInterface[]
     */
    public function getTypes(): iterable
    {
        return $this->types;
    }

    public function getType(string $type_name): TypeInterface
    {
        if (isset($this->types[$type_name])) {
            return $this->types[$type_name];
        } else {
            throw new InvalidArgumentException("Type '$type_name' not found");
        }
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
        } else {
            throw new InvalidArgumentException("Type '$type_name' already added");
        }
    }

    /**
     * @var RecordInterface|array
     */
    private $records = [];

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
     *
     * @param  string             $table_name
     * @param  array              $record
     * @param  string             $comment
     * @param  bool               $auto_set_created_at
     * @param  bool               $auto_set_updated_at
     * @return StructureInterface
     */
    private function &addTableRecord(string $table_name, array $record, string $comment = '', $auto_set_created_at = false, $auto_set_updated_at = false): StructureInterface
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
     *
     * @param  string             $table_name
     * @param  array              $field_names
     * @param  array              $records_to_add
     * @param  string             $comment
     * @param  bool               $auto_set_created_at
     * @param  bool               $auto_set_updated_at
     * @return StructureInterface
     */
    private function &addTableRecords(string $table_name, array $field_names, array $records_to_add, string $comment = '', $auto_set_created_at = false, $auto_set_updated_at = false): StructureInterface
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

    /**
     * @var array
     */
    private $config = [];

    public function getConfig(string $name, $default = null)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : $default;
    }

    public function setConfig(string $name, $value): StructureInterface
    {
        $this->config[$name] = $value;

        return $this;
    }

    private $namespace = null;

    public function getNamespace(): string
    {
        if ($this->namespace === null) {
            $this->namespace = (new ReflectionClass(get_class($this)))->getNamespaceName();
        }

        return $this->namespace;
    }

    public function setNamespace($namespace): StructureInterface
    {
        if ($namespace === null || is_string($namespace)) {
            $this->namespace = $namespace;
        } else {
            throw new InvalidArgumentException("Namespace '$namespace' is not valid");
        }

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

    private $builders = [];

    private function getBuilders(
        string $build_path = null,
        ConnectionInterface $connection = null,
        array $event_handlers = []
    ): array
    {
        if (empty($this->builders)) {
            $this->builders[] = new BaseDirBuilder($this);
            $this->builders[] = new SqlDirBuilder($this);

            $this->builders[] = new TypesBuilder($this);

            //$this->builders[] = new TypeDirBuilder($this);
            $this->builders[] = new BaseTypeClassBuilder($this);
            $this->builders[] = new TypeClassBuilder($this);
            $this->builders[] = new TypeTableBuilder($this);

            $this->builders[] = new AssociationsBuilder($this);
            $this->builders[] = new TriggersBuilder($this);
            $this->builders[] = new RecordsBuilder($this);

            $this->builders[] = new ManagerDirBuilder($this);
            $this->builders[] = new BaseManagerDirBuilder($this);
            $this->builders[] = new BaseTypeManagerBuilder($this);
            $this->builders[] = new TypeManagerBuilder($this);

            $this->builders[] = new CollectionDirBuilder($this);
            $this->builders[] = new BaseCollectionDirBuilder($this);
            $this->builders[] = new BaseTypeCollectionBuilder($this);
            $this->builders[] = new TypeCollectionBuilder($this);

            foreach ($this->builders as $k => $v) {
                foreach ($event_handlers as $event => $handler) {
                    $v->registerEventHandler($event, $handler);
                }

                if ($build_path && $v instanceof FileSystemBuilderInterface) {
                    $this->builders[$k]->setBuildPath($build_path);
                }

                if ($connection && $v instanceof DatabaseBuilderInterface) {
                    $this->builders[$k]->setConnection($connection);
                }
            }
        }

        return $this->builders;
    }
}
