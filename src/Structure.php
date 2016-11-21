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
use ActiveCollab\DatabaseStructure\Builder\TriggersBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeClassBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeCollectionBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeManagerBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypesBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
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
     * @var Type[]
     */
    private $types = [];

    /**
     * Get all structure type.
     *
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Return type by type name.
     *
     * @param  string $type_name
     * @return Type
     */
    public function getType($type_name)
    {
        if (isset($this->types[$type_name])) {
            return $this->types[$type_name];
        } else {
            throw new InvalidArgumentException("Type '$type_name' not found");
        }
    }

    /**
     * @param  string $type_name
     * @return Type
     */
    protected function &addType($type_name)
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
     * @var array
     */
    private $config = [];

    /**
     * Return a config option value.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getConfig($name, $default = null)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : $default;
    }

    /**
     * Set a config option option value.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function &setConfig($name, $value)
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

            $this->builders[] = new TypesBuilder($this);
            $this->builders[] = new BaseTypeClassBuilder($this);
            $this->builders[] = new TypeClassBuilder($this);
            $this->builders[] = new TypeTableBuilder($this);

            $this->builders[] = new AssociationsBuilder($this);
            $this->builders[] = new TriggersBuilder($this);

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
