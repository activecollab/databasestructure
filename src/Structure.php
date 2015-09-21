<?php

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Field\Scalar\Field as ScalarField;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use RuntimeException;

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
     * @param callable|null $on_base_dir_created
     * @param callable|null $on_class_built
     * @param callable|null $on_class_build_skipped
     */
    public function build($build_path = null, callable $on_base_dir_created = null, callable $on_class_built = null, callable $on_class_build_skipped = null)
    {
        if ($build_path) {
            if (is_dir($build_path)) {
                $build_path = rtrim($build_path, DIRECTORY_SEPARATOR);

                if (!is_dir("$build_path/Base")) {
                    $old_umask = umask(0);
                    $dir_created = mkdir("$build_path/Base");
                    umask($old_umask);

                    if ($dir_created) {
                        if ($on_base_dir_created && is_callable($on_base_dir_created)) {
                            call_user_func($on_base_dir_created, "$build_path/Base");
                        }
                    } else {
                        throw new RuntimeException("Failed to create '$build_path/Base' directory");
                    }
                }
            } else {
                throw new InvalidArgumentException("Directory '$build_path' not found");
            }
        }

        foreach ($this->types as $type) {
            $this->buildBaseTypeClass($type, $build_path, $on_class_built);
            $this->buildTypeClass($type, $build_path, $on_class_built, $on_class_build_skipped);
        }
    }

    /**
     * Build base type class
     *
     * @param Type          $type
     * @param string        $build_path
     * @param callable|null $on_class_built
     */
    private function buildBaseTypeClass(Type $type, $build_path, callable $on_class_built = null)
    {
        $base_class_name = Inflector::classify(Inflector::singularize($type->getName()));
        $base_class_extends = '\\' . ltrim($type->getBaseClassExtends(), '\\');

        $base_class_build_path = $build_path ? "$build_path/Base/$base_class_name.php" : null;

        $result = [];

        $result[] = "<?php";
        $result[] = '';

        if ($this->getNamespace()) {
            $result[] = 'namespace ' . $this->getNamespace() . '\\Base;';
        } else {
            $result[] = 'namespace Base;';
        }

        $result[] = '';

        $interfaces = $traits = [];

        foreach ($type->getTraits() as $interface => $implementations) {
            if ($interface != '--just-paste-trait--') {
                $interfaces[] = '\\' . ltrim($interface, '\\');
            }

            if (count($implementations)) {
                foreach ($implementations as $implementation) {
                    $traits[] = '\\' . ltrim($implementation, '\\');;
                }
            }
        }

        $result[] = 'abstract class ' . $base_class_name . ' extends ' . $base_class_extends . (empty($interfaces) ? '' : ' implements ' . implode(', ', $interfaces));
        $result[] = '{';

        if (count($traits)) {
            $trait_tweaks_count = count($type->getTraitTweaks());

            $result[] = '    use ' . implode(', ', $traits) . ($trait_tweaks_count ? '{' : ';');

            if ($trait_tweaks_count) {
                for ($i = 0; $i < $trait_tweaks_count - 1; $i++) {
                    $result[] = '        ' . $type->getTraitTweaks()[$i] . ($i < $trait_tweaks_count - 2 ? ',' : '');
                }

                $result[] = '    }';
            }

            $result[] = '';
        }

        $result[] = '    /**';
        $result[] = '     * Name of the table where records are stored';
        $result[] = '     *';
        $result[] = '     * @var string';
        $result[] = '     */';
        $result[] = '    protected $table_name = ' . var_export($type->getTableName(), true) . ';';

        $fields = $type->getAllFields();

        $stringified_field_names = [];
        $fields_with_default_value = [];

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel()) {
                $stringified_field_names[] = var_export($field->getName(), true);

                if ($field->getName() != 'id' && $field->getDefaultValue() !== null) {
                    $fields_with_default_value[$field->getName()] = $field->getDefaultValue();
                }
            }
        }

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * All table fields';
        $result[] = '     *';
        $result[] = '     * @var array';
        $result[] = '     */';
        $result[] = '    protected $fields = [' . implode(', ', $stringified_field_names) . '];';

        if (count($fields_with_default_value)) {
            $result[] = '';
            $result[] = '    /**';
            $result[] = '     * List of default field values';
            $result[] = '     *';
            $result[] = '     * @var array';
            $result[] = '     */';
            $result[] = '     protected $default_field_values = [';

            foreach ($fields_with_default_value as $field_name => $default_value) {
                $result[] = '       ' . var_export($field_name, true) . ' => ' . var_export($default_value, true) . ',';
            }

            $result[] = '     ];';
        }

        foreach ($type->getAssociations() as $association) {
            $association->buildClassMethods($this->getNamespace(), $type, $this->getType($association->getTargetTypeName()), $result);
        }

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel() && $field->getName() != 'id') {
                $camelized_field_name = Inflector::classify($field->getName());

                $result[] = '';
                $result[] = '    /**';
                $result[] = '     * Return value of ' . $field->getName() . ' field';
                $result[] = '     *';
                $result[] = '     * @return ' . $field->getNativeType();
                $result[] = '     */';
                $result[] = '    public function get' . $camelized_field_name . '()';
                $result[] = '    {';
                $result[] = '        return $this->getFieldValue(' . var_export($field->getName(), true) . ');';
                $result[] = '    }';
                $result[] = '';
                $result[] = '    /**';
                $result[] = '     * Set value of ' . $field->getName() . '  field';
                $result[] = '     *';
                $result[] = '     * @param  ' . $field->getNativeType() . ' $value';
                $result[] = '     * @return $this';
                $result[] = '     */';
                $result[] = '    public function &set' . $camelized_field_name . '($value)';
                $result[] = '    {';
                $result[] = '        $this->setFieldValue(' . var_export($field->getName(), true) . ', $value);';
                $result[] = '';
                $result[] = '        return $this;';
                $result[] = '    }';
            }
        }

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Set value of specific field';
        $result[] = '     *';
        $result[] = '     * @param  string                    $name';
        $result[] = '     * @param  mixed                     $value';
        $result[] = '     * @return $this';
        $result[] = '     * @throws \\InvalidArgumentException';
        $result[] = '     */';
        $result[] = '    public function &setFieldValue($name, $value)';
        $result[] = '    {';
        $result[] = '        if ($value === null) {';
        $result[] = '            return parent::setFieldValue($name, null);';
        $result[] = '        } else {';
        $result[] = '            switch ($name) {';

        $casters = [];

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel()) {
                $casting_code = $field->getCastingCode('value');

                if (empty($casters[$casting_code])) {
                    $casters[$casting_code] = [];
                }

                $casters[$casting_code][] = $field->getName();
            }
        }

        foreach ($casters as $caster_code => $casted_field_names) {
            foreach ($casted_field_names as $casted_field_name) {
                $result[] = '                case ' . var_export($casted_field_name, true) . ':';
            }

            $result[] = '                    return parent::setFieldValue($name, ' . $caster_code . ');';
        }

        $result[] = '                default:';
        $result[] = '                    throw new \\InvalidArgumentException("Field $name does not exist in this table");';
        $result[] = '            }';
        $result[] = '        }';
        $result[] = '    }';

        $result[] = '}';

        $result = implode("\n", $result);

        if ($build_path) {
            file_put_contents($base_class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        if (is_callable($on_class_built)) {
            call_user_func($on_class_built, $base_class_name, $base_class_build_path);
        }
    }

    /**
     * Build type class
     *
     * @param Type          $type
     * @param string        $build_path
     * @param callable|null $on_class_built
     * @param callable|null $on_class_build_skipped
     */
    private function buildTypeClass(Type $type, $build_path, callable $on_class_built = null, callable $on_class_build_skipped = null)
    {
        $class_name = Inflector::classify(Inflector::singularize($type->getName()));
        $base_class_name = 'Base\\' . $class_name;

        $class_build_path = $build_path ? "$build_path/$class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            if (is_callable($on_class_build_skipped)) {
                call_user_func($on_class_build_skipped, $class_name, $class_build_path);
            }
        }

        $result = [];

        $result[] = "<?php";
        $result[] = '';

        if ($this->getNamespace()) {
            $result[] = 'namespace ' . $this->getNamespace() . ';';
            $result[] = '';
        }

        $result[] = 'class ' . $class_name . ' extends ' . $base_class_name;
        $result[] = '{';
        $result[] = '}';

        $result = implode("\n", $result);

        if ($build_path) {
            file_put_contents($class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        if (is_callable($on_class_built)) {
            call_user_func($on_class_built, $class_name, $class_build_path);
        }
    }
}