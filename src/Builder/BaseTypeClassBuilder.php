<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Field\Scalar\Field as ScalarField;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class BaseTypeClassBuilder extends FileSystemBuilder
{
    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
        $base_class_name = Inflector::classify(Inflector::singularize($type->getName()));
        $base_class_extends = '\\' . ltrim($type->getBaseClassExtends(), '\\');

        $base_class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/Base/$base_class_name.php" : null;

        $result = [];

        $result[] = "<?php";
        $result[] = '';

        $base_class_namespace = $this->getStructure()->getNamespace() ? $this->getStructure()->getNamespace() . '\\Base' : 'Base';

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = '/**';
        $result[] = ' * @package ' . $base_class_namespace;
        $result[] = ' */';

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
            $association->buildClassMethods($this->getStructure()->getNamespace(), $type, $this->getStructure()->getType($association->getTargetTypeName()), $result);
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
        $result[] = '            parent::setFieldValue($name, null);';
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
        $result[] = '';
        $result[] = '        return $this;';
        $result[] = '    }';

        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent('on_class_built', [$base_class_name, $base_class_build_path]);
    }
}