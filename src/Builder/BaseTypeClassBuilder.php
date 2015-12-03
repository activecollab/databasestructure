<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Field as ScalarField;
use ActiveCollab\DatabaseStructure\Field\Composite\Field as CompositeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
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
            $result[] = '    protected $default_field_values = [';

            foreach ($fields_with_default_value as $field_name => $default_value) {
                $result[] = '       ' . var_export($field_name, true) . ' => ' . var_export($default_value, true) . ',';
            }

            $result[] = '     ];';
        }

        if ($type->getOrderBy() != ['id']) {
            $result[] = '';
            $result[] = '    /**';
            $result[] = '     * @var string[]';
            $result[] = '     */';
            $result[] = '    protected $order_by = [' . implode(', ', array_map(function($value) {
                return var_export($value, true);
            }, $type->getOrderBy())) . '];';
        }

        foreach ($type->getAssociations() as $association) {
            $association->buildClassMethods($this->getStructure()->getNamespace(), $type, $this->getStructure()->getType($association->getTargetTypeName()), $result);
        }

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel() && $field->getName() != 'id') {
                $result[] = '';
                $result[] = '    /**';
                $result[] = '     * Return value of ' . $field->getName() . ' field';
                $result[] = '     *';
                $result[] = '     * @return ' . $field->getNativeType();
                $result[] = '     */';
                $result[] = '    public function ' . $this->getGetterName($field->getName()) . '()';
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
                $result[] = '    public function &' . $this->getSetterName($field->getName()) . '($value)';
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

        $this->buildJsonSerialize($type->getSerialize(), '    ', $result);
        $this->buildCompositeFieldMethods($type->getFields(), '    ', $result);
        $this->buildValidate($type->getFields(), $type->getAssociations(), '    ', $result);

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

    /**
     * @param FieldInterface[] $fields
     * @param string           $indent
     * @param array            $result
     */
    public function buildCompositeFieldMethods($fields, $indent, array &$result)
    {
        foreach ($fields as $field) {
            if ($field instanceof CompositeField) {
                $field->getBaseClassMethods($indent, $result);
            }
        }
    }

    /**
     * Build JSON serialize method, if we need to serialize extra fields
     *
     * @param array  $serialize
     * @param string $indent
     * @param array  $result
     */
    public function buildJsonSerialize(array $serialize, $indent, array &$result)
    {
        if (count($serialize)) {
            $result[] = '';
            $result[] = $indent . '/**';
            $result[] = $indent . ' * Prepare object properties so they can be serialized to JSON';
            $result[] = $indent . ' *';
            $result[] = $indent . ' * @return array';
            $result[] = $indent . ' */';
            $result[] = $indent . 'public function jsonSerialize()';
            $result[] = $indent . '{';
            $result[] = $indent . '    return array_merge(parent::jsonSerialize(), [';

            foreach ($serialize as $field) {
                $result[] = $indent . '        ' . var_export($field, true) . ' => $this->' . $this->getGetterName($field) . '(), ';
            }

            $result[] = $indent . '    ]);';
            $result[] = $indent . '}';
        }
    }

    /**
     * @param FieldInterface[]       $fields
     * @param AssociationInterface[] $associations
     * @param string                 $indent
     * @param array                  $result
     */
    private function buildValidate(array $fields, array $associations, $indent, array &$result)
    {
        $fields_to_validate = $fields;

        foreach ($associations as $association) {
            if (count($association->getFields())) {
                $fields_to_validate = array_merge($fields_to_validate, $association->getFields());
            }
        }

        $validator_lines = [];
        $line_indent = $indent  . '    ';

        foreach ($fields_to_validate as $field) {
            if ($field instanceof CompositeField) {
                $field->getValidatorLines($line_indent, $validator_lines);

                foreach ($field->getFields() as $subfield) {
                    if ($subfield instanceof ScalarField && $subfield->getShouldBeAddedToModel()) {
                        $this->buildValidatePresenceLinesForScalarField($subfield, $line_indent, $validator_lines);
                    }
                }
            } elseif ($field instanceof ScalarField && $field->getShouldBeAddedToModel()) {
                $this->buildValidatePresenceLinesForScalarField($field, $line_indent, $validator_lines);
            }
        }

        if (count($validator_lines)) {
            $result[] = '';
            $result[] = $indent . '/**';
            $result[] = $indent . ' * Validate object properties before object is saved';
            $result[] = $indent . ' *';
            $result[] = $indent . ' * @param \ActiveCollab\DatabaseObject\ValidatorInterface $validator';
            $result[] = $indent . ' */';
            $result[] = $indent . 'public function validate(\ActiveCollab\DatabaseObject\ValidatorInterface &$validator)';
            $result[] = $indent . '{';

            $result = array_merge($result, $validator_lines);

            $result[] = '';
            $result[] = $indent . '    parent::validate($validator);';
            $result[] = $indent . '}';
        }
    }

    /**
     * Build validate lines for scalar fields
     *
     * @param ScalarField $field
     * @param string      $line_indent
     * @param array       $validator_lines
     */
    private function buildValidatePresenceLinesForScalarField(ScalarField $field, $line_indent, array &$validator_lines)
    {
        if ($field instanceof RequiredInterface && $field instanceof UniqueInterface) {
            if ($field->isRequired() && $field->isUnique()) {
                $validator_lines[] = $line_indent . $this->buildValidatePresenceAndUniquenessLine($field->getName(), $field->getUniquenessContext());
            } elseif ($field->isRequired()) {
                $validator_lines[] = $line_indent . $this->buildValidatePresenceLine($field->getName());
            } elseif ($field->isUnique()) {
                $validator_lines[] = $line_indent . $this->buildValidateUniquenessLine($field->getName(), $field->getUniquenessContext());
            }
        } elseif($field instanceof RequiredInterface && $field->isRequired()) {
            $validator_lines[] = $line_indent . $this->buildValidatePresenceLine($field->getName());
        } elseif($field instanceof UniqueInterface && $field->isUnique()) {
            $validator_lines[] = $line_indent . $this->buildValidateUniquenessLine($field->getName(), $field->getUniquenessContext());
        }
    }

    /**
     * Build validator value presence line
     *
     * @param  string $field_name
     * @return string
     */
    private function buildValidatePresenceLine($field_name)
    {
        return '$validator->present(' . var_export($field_name, true) . ');';
    }

    /**
     * Build validator uniqueness line
     *
     * @param  string $field_name
     * @param  array  $context
     * @return string
     */
    private function buildValidateUniquenessLine($field_name, array $context)
    {
        $field_names = [var_export($field_name, true)];

        foreach ($context as $v) {
            $field_names[] = var_export($v, true);
        }

        return '$validator->unique(' . implode(', ', $field_names) . ');';
    }

    /**
     * Build validator uniqueness line
     *
     * @param  string $field_name
     * @param  array  $context
     * @return string
     */
    private function buildValidatePresenceAndUniquenessLine($field_name, array $context)
    {
        $field_names = [var_export($field_name, true)];

        foreach ($context as $v) {
            $field_names[] = var_export($v, true);
        }

        return '$validator->presentAndUnique(' . implode(', ', $field_names) . ');';
    }

    /**
     * @var array
     */
    private $getter_names = [], $setter_names = [];

    /**
     * @param  string $field_name
     * @return string
     */
    private function getGetterName($field_name)
    {
        if (empty($this->getter_names[$field_name])) {
            $camelized_field_name = Inflector::classify($field_name);

            $this->getter_names[$field_name] = "get{$camelized_field_name}";
            $this->setter_names[$field_name] = "set{$camelized_field_name}";
        }

        return $this->getter_names[$field_name];
    }

    /**
     * @param  string $field_name
     * @return string
     */
    private function getSetterName($field_name)
    {
        if (empty($this->setter_names[$field_name])) {
            $camelized_field_name = Inflector::classify($field_name);

            $this->getter_names[$field_name] = "get{$camelized_field_name}";
            $this->setter_names[$field_name] = "set{$camelized_field_name}";
        }

        return $this->setter_names[$field_name];
    }
}
