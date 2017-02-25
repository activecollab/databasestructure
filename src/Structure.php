<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\Builder\AssociationsBuilder;
use ActiveCollab\DatabaseStructure\Builder\BaseCollectionDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\BaseDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\BaseManagerDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\BaseTypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\BaseTypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\BaseTypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\CollectionDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\DatabaseBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\ManagerDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\RecordsBuilder;
use ActiveCollab\DatabaseStructure\Builder\SqlDirBuilder;
use ActiveCollab\DatabaseStructure\Builder\TriggersBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypesBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Field\Composite\CreatedAtField;
use ActiveCollab\DatabaseStructure\Field\Composite\UpdatedAtField;
use ActiveCollab\DateValue\DateTimeValue;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
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
    abstract protected function configure();

    /**
     * @var iterable|TypeInterface[]
     */
    private $types = [];

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

        foreach ($type->getFields() as $field) {
            if ($field instanceof CreatedAtField && !array_key_exists('created_at', $record)) {
                $record['created_at'] = new DateTimeValue();
            } elseif ($field instanceof UpdatedAtField && !array_key_exists('updated_at', $record)) {
                $record['updated_at'] = new DateTimeValue();
            }
        }

        return $this->addTableRecord($type->getTableName(), $record, $comment);
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

        foreach ($type->getFields() as $field) {
            if ($field instanceof CreatedAtField) {
                if (!in_array('created_at', $field_names)) {
                    $field_names[] = 'created_at';
                }

                foreach ($records_to_add as $k => $v) {
                    if (!array_key_exists('created_at', $v)) {
                        $records_to_add[$k]['created_at'] = new DateTimeValue();
                    }
                }
            } elseif ($field instanceof UpdatedAtField) {
                if (!in_array('updated_at', $field_names)) {
                    $field_names[] = 'updated_at';
                }

                foreach ($records_to_add as $k => $v) {
                    if (!array_key_exists('updated_at', $v)) {
                        $records_to_add[$k]['updated_at'] = new DateTimeValue();
                    }
                }
            }
        }

        return $this->addTableRecords($type->getTableName(), $field_names, $records_to_add, $comment);
    }

    /**
     * Add a record to the initial data set.
     *
     * @param  string             $table_name
     * @param  array              $record
     * @param  string             $comment
     * @return StructureInterface
     */
    private function &addTableRecord(string $table_name, array $record, string $comment = ''): StructureInterface
    {
        $this->records[] = new SingleRecord($table_name, $record, $comment);

        return $this;
    }

    /**
     * Add multiple records to the initial data set.
     *
     * @param  string             $table_name
     * @param  array              $field_names
     * @param  array              $records_to_add
     * @param  string             $comment
     * @return StructureInterface
     */
    private function &addTableRecords(string $table_name, array $field_names, array $records_to_add, string $comment = ''): StructureInterface
    {
        $this->records[] = new MultiRecord($table_name, $field_names, $records_to_add, $comment);

        return $this;
    }

    /**
     * @var array
     */
    private $config = [];

    /**
     * {@inheritdoc}
     */
    public function getConfig(string $name, $default = null)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function &setConfig($name, $value): StructureInterface
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * @var string
     */
    private $namespace = null;

    /**
     * @return string
     */
    public function getNamespace()
    {
        if ($this->namespace === null) {
            $this->namespace = (new \ReflectionClass(get_class($this)))->getNamespaceName();
        }

        return $this->namespace;
    }

    /**
     * @param  string|null $namespace
     * @return $this
     */
    public function &setNamespace($namespace)
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

    /**
     * Build model at the given path.
     *
     * If $build_path is null, classes will be generated, evaled and loaded into the memory
     *
     * @param string|null         $build_path
     * @param ConnectionInterface $connection
     * @param array|null          $event_handlers
     */
    public function build($build_path = null, ConnectionInterface $connection = null, array $event_handlers = [])
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
    private $builders = [];

    /**
     * Return a list of prepared builder instances.
     *
     * @param  string|null         $build_path
     * @param  ConnectionInterface $connection
     * @param  array               $event_handlers
     * @return BuilderInterface[]
     */
    private function getBuilders($build_path, ConnectionInterface $connection = null, array $event_handlers)
    {
        if (empty($this->builders)) {
            $this->builders[] = new BaseDirBuilder($this);
            $this->builders[] = new SqlDirBuilder($this);

            $this->builders[] = new TypesBuilder($this);
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
                foreach ($this->builders as $k => $v) {
                    $v->registerEventHandler($event, $handler);
                }
            }
        }

        return $this->builders;
    }
}
