<?php

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Builder\BaseDir;
use ActiveCollab\DatabaseStructure\Builder\BaseTypeClass;
use ActiveCollab\DatabaseStructure\Builder\FileSystem;
use ActiveCollab\DatabaseStructure\Builder\TypeClass;
use ActiveCollab\DatabaseStructure\Builder\Types;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
abstract class Structure
{
    /**
     * Construct a new instance
     */
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Configure types, fields and associations
     */
    abstract protected function configure();

    /**
     * @var Type[]
     */
    private $types = [];

    /**
     * Get all structure type
     *
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Return type by type name
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

            return $this->types[$type_name];
        } else {
            throw new InvalidArgumentException("Type '$type_name' already added");
        }
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
     * Build model at the given path
     *
     * If $build_path is null, classes will be generated, evaled and loaded into the memory
     *
     * @param string|null   $build_path
     * @param array|null    $event_handlers
     * @param callable|null $on_base_dir_created
     * @param callable|null $on_class_built
     * @param callable|null $on_class_build_skipped
     */
    public function build($build_path = null, array $event_handlers = [], callable $on_base_dir_created = null, callable $on_class_built = null, callable $on_class_build_skipped = null)
    {
        $builders = $this->getBuilders($build_path, $event_handlers);

        foreach ($this->types as $type) {
            foreach ($builders as $builder) {
                $builder->build($type);
            }
        }
    }

    /**
     * @var BuilderInterface[]
     */
    private $builders = [];

    /**
     * Return a list of prepared builder instances
     *
     * @param  string|null        $build_path
     * @param  array              $event_handlers
     * @return BuilderInterface[]
     */
    private function getBuilders($build_path = null, array $event_handlers)
    {
        if (empty($this->builders)) {
            $this->builders[] = new BaseDir($this);
            $this->builders[] = new Types($this);
            $this->builders[] = new BaseTypeClass($this);
            $this->builders[] = new TypeClass($this);

            if ($build_path) {
                foreach ($this->builders as $k => $v) {
                    if ($v instanceof FileSystem) {
                        $v->setBuildPath($build_path);
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

//    public function buildDatabaseTables(Connection $connection = null, callable $on_table_added = null, callable $on_foreign_key_added = null)
//    {
//        foreach ($this->getTypes() as $type) {
//            $create_table_statement = $type->getCreateTableStatement();
//
//            var_dump($create_table_statement);
//        }
//    }
}