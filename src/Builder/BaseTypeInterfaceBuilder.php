<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\Record\ValueCaster;
use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\CompositeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonFieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarFieldWithDefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\DateValue\DateValueInterface;
use Doctrine\Common\Inflector\Inflector;
use Throwable;

class BaseTypeInterfaceBuilder extends FileSystemBuilder
{
    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
        $base_interface_name = sprintf(
            '%sInterface',
            Inflector::classify(Inflector::singularize($type->getName()))
        );
        $base_interface_extends = '\\' . ltrim($type->getBaseInterfaceExtends(), '\\');

        $base_interface_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Base/$base_interface_name.php"
            : null;

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        if ($this->getStructure()->getConfig('header_comment')) {
            $result = array_merge($result, explode("\n", $this->getStructure()->getConfig('header_comment')));
            $result[] = '';
        }

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        $base_interface_namespace = $this->getStructure()->getNamespace()
            ? $this->getStructure()->getNamespace() . '\\Base'
            : 'Base';

        $result[] = 'namespace ' . $base_interface_namespace . ';';
        $result[] = '';

        $interfaces = [
            $base_interface_extends,
        ];

        foreach (array_keys($type->getTraits()) as $interface) {
            if ($interface !== '--just-paste-trait--') {
                $interfaces[] = '\\' . ltrim($interface, '\\');
            }
        }

        $this->buildInterfaceDeclaration($base_interface_name, $interfaces, '', $result);

        $result[] = '{';

        $fields = $type->getAllFields();

        foreach ($fields as $field) {
            if ($field instanceof GeneratedInterface && $field->isGenerated()) {
                continue;
            }

            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel() && $field->getName() != 'id') {
                $this->buildFieldGetterAndSetter($field, '    ', $result);
            }
        }

        foreach ($type->getGeneratedFields() as $field_name => $caster) {
            $this->buildGeneratedFieldGetter($field_name, $caster, '    ', $result);
        }

        $this->buildCompositeFieldMethods($type->getFields(), '    ', $result);

        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_interface_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent('on_interface_built', [$base_interface_name, $base_interface_build_path]);
    }

    public function buildInterfaceDeclaration(
        string $base_interface_name,
        array $interfaces,
        string $indent,
        array &$result
    ): void
    {
        $result[] = sprintf(
            '%sinterface %s extends %s',
            $indent,
            $base_interface_name,
            implode(', ', $interfaces)
        );
    }

    /**
     * Build field definitions.
     *
     * @param FieldInterface[] $fields
     * @param string           $indent
     * @param array            $result
     */
    private function buildFields(array $fields, $indent, array &$result)
    {
        $stringified_field_names = [];
        $fields_with_default_value = [];

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel()) {
                $stringified_field_names[] = var_export($field->getName(), true);

                if ($field->getName() != 'id'
                    && ($field instanceof DefaultValueInterface && $field->getDefaultValue() !== null)) {
                    $fields_with_default_value[$field->getName()] = $field->getDefaultValue();
                }
            }
        }

        $result[] = '';
        $result[] = $indent . '/**';
        $result[] = $indent . ' * Table fields that are managed by this entity.';
        $result[] = $indent . ' *';
        $result[] = $indent . ' * @var array';
        $result[] = $indent . ' */';
        $result[] = $indent . 'protected $fields = [';

        foreach ($stringified_field_names as $stringified_field_name) {
            $result[] = $indent . '    ' . $stringified_field_name . ',';
        }

        $result[] = $indent . '];';

        if (count($fields_with_default_value)) {
            $result[] = '';
            $result[] = $indent . '/**';
            $result[] = $indent . ' * List of default field values.';
            $result[] = $indent . ' *';
            $result[] = $indent . ' * @var array';
            $result[] = $indent . ' */';
            $result[] = $indent . 'protected $default_field_values = [';

            foreach ($fields_with_default_value as $field_name => $default_value) {
                $result[] = $indent . '   ' . var_export($field_name, true) . ' => ' . var_export($default_value, true) . ',';
            }

            $result[] = $indent . '];';
        }
    }

    /**
     * Build a list of generated fields.
     *
     * @param string[] $generated_field_names
     * @param string   $indent
     * @param array    $result
     */
    public function buildGeneratedFields(array $generated_field_names, $indent, array &$result)
    {
        $result[] = '';
        $result[] = $indent . '/**';
        $result[] = $indent . ' * Generated fields that are loaded, but not managed by the entity..';
        $result[] = $indent . ' *';
        $result[] = $indent . ' * @var array';
        $result[] = $indent . ' */';
        $result[] = $indent . 'protected $generated_fields = [' . implode(', ', array_map(function ($field_name) {
            return var_export($field_name, true);
        }, $generated_field_names)) . '];';
    }

    public function buildConfigureMethod(array $generated_fields, $indent, array &$result)
    {
        if (!empty($generated_fields)) {
            $result[] = '';
            $result[] = $indent . 'protected function configure()';
            $result[] = $indent . '{';
            $result[] = $indent . '    $this->setGeneratedFieldsValueCaster(new \\' . ValueCaster::class . '([';

            foreach ($generated_fields as $field_name => $caster) {
                switch ($caster) {
                    case ValueCasterInterface::CAST_INT:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_INT';
                        break;
                    case ValueCasterInterface::CAST_FLOAT:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_FLOAT';
                        break;
                    case ValueCasterInterface::CAST_BOOL:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_BOOL';
                        break;
                    case ValueCasterInterface::CAST_DATE:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_DATE';
                        break;
                    case ValueCasterInterface::CAST_DATETIME:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_DATETIME';
                        break;
                    case ValueCasterInterface::CAST_JSON:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_JSON';
                        break;
                    default:
                        $full_caster = '\\' . ValueCasterInterface::class . '::CAST_STRING';
                }

                $result[] = $indent . '        ' . var_export($field_name, true) . ' => ' . $full_caster . ',';
            }

            $result[] = $indent . '    ]));';
            $result[] = $indent . '}';
        }
    }

    /**
     * @param TypeInterface $source_type
     * @param string        $indent
     * @param array         $result
     */
    public function buildAssociatedEntitiesManagers(
        TypeInterface $source_type,
        string $indent,
        array &$result
    )
    {
        $associations = $source_type->getAssociations();

        if (!empty($associations)) {
            $result[] = '';
            $result[] = $indent . 'private $associated_entities_managers;';
        }

        $result[] = '';
        $result[] = $indent . 'protected function getAssociatedEntitiesManagers(): array';
        $result[] = $indent . '{';

        if (empty($associations)) {
            $result[] = $indent . '    return [];';
        } else {
            $result[] = $indent . '    if ($this->associated_entities_managers === null) {';
            $result[] = $indent . '        $this->associated_entities_managers  = [';

            foreach ($associations as $association) {
                $association->buildAssociatedEntitiesManagerConstructionLine(
                    $this->getStructure(),
                    $source_type,
                    $this->getStructure()->getType($association->getTargetTypeName()),
                    $indent . '            ',
                    $result
                );
            }

            $result[] = $indent . '        ];';
            $result[] = $indent . '    }';
            $result[] = '';
            $result[] = $indent . '    return $this->associated_entities_managers;';
        }

        $result[] = $indent . '}';
    }

    /**
     * Build setAttributes() method.
     *
     * @param TypeInterface $source_type
     * @param string        $indent
     * @param array         $result
     */
    public function buildSetAttributes(TypeInterface $source_type, $indent, array &$result)
    {
        $associations = $source_type->getAssociations();

        $attribute_interception_lines = [];

        if (!empty($associations)) {
            foreach ($associations as $association) {
                $association->buildAttributeInterception(
                    $this->getStructure(),
                    $source_type,
                    $this->getStructure()->getType($association->getTargetTypeName()),
                    $indent . '        ',
                    $attribute_interception_lines
                );
            }
        }

        if (!empty($attribute_interception_lines)) {
            $result[] = '';
            $result[] = $indent . 'public function &setAttribute($attribute, $value)';
            $result[] = $indent . '{';
            $result[] = $indent . '    switch ($attribute) {';

            foreach ($attribute_interception_lines as $attribute_interception_line) {
                $result[] = $attribute_interception_line;
            }

            $result[] = $indent . '    }';
            $result[] = '';
            $result[] = $indent . '    return parent::setAttribute($attribute, $value);';
            $result[] = $indent . '}';
        }
    }

    private function buildCompositeFieldMethods(
        iterable $fields,
        string $indent,
        array &$result
    ): void
    {
        foreach ($fields as $field) {
            if ($field instanceof CompositeField) {
                $field->getBaseInterfaceMethods($indent, $result);
            }
        }
    }

    /**
     * @param ScalarField $field
     * @param string      $indent
     * @param array       $result
     */
    private function buildFieldGetterAndSetter(ScalarField $field, $indent, array &$result): void
    {
        $lines = [];

        $short_getter = null;

        $default_value = $field instanceof ScalarFieldWithDefaultValueInterface ? $field->getDefaultValue() : null;

        $type_for_executable_code = $this->getTypeForExecutableCode(
            $field->getNativeType(),
            $default_value,
            $field->isRequired()
        );
        $type_for_doc_block = $this->getTypeForDocBlock(
            $field->getNativeType(),
            $default_value,
            $field->isRequired()
        );

        if ($field instanceof BooleanField && $this->useShortGetterName($field->getName())) {
            $short_getter = $this->getShortGetterName($field->getName());

            $lines[] = sprintf(
                'public function %s()%s%s;',
                $short_getter,
                $type_for_executable_code ? ': ' : '',
                $type_for_executable_code
            );
        }

        if ($short_getter && $this->getStructure()->getConfig('deprecate_long_bool_field_getter')) {
            $lines[] = '/**';
            $lines[] = " * @deprecated use $short_getter()";
            $lines[] = ' */';
        }

        $lines[] = 'public function ' . $this->getGetterName($field->getName()) . '()' . ($type_for_executable_code ? ': ' : '') . $type_for_executable_code . ';';

        if (!$field->getProtectSetter()) {
            $lines[] = '';
            $lines[] = '/**';
            $lines[] = ' * Set value of ' . $field->getName() . ' field.';
            $lines[] = ' *';
            $lines[] = ' * @param  ' . str_pad($type_for_doc_block, 5, ' ', STR_PAD_RIGHT) . ' $value';
            $lines[] = ' * @return $this';
            $lines[] = ' */';
            $lines[] = 'public function &' . $this->getSetterName($field->getName()) . '(' . $type_for_executable_code . ($type_for_executable_code ? ' ' : '') . '$value)' . ';';

            if ($field instanceof JsonFieldInterface) {
                $lines[] = '';
                $lines[] = '/**';
                $lines[] = ' * Modify value of ' . $field->getName() . ' field.';
                $lines[] = ' *';
                $lines[] = ' * @param  callable $callback';
                $lines[] = ' * @param  bool     $force_array';
                $lines[] = ' * @return $this';
                $lines[] = ' */';
                $lines[] = 'public function &' . $this->getModifierName($field->getName()) . '(callable $callback, bool $force_array = false)' . ';';
            }
        }

        foreach ($lines as $line) {
            $result[] = $line ? $indent . $line : '';
        }
    }

    private function getTypeForExecutableCode(
        string $native_type,
        $default_value,
        bool $field_is_required
    ): string
    {
        $result = '';

        if ($native_type != 'mixed') {
            $result = ($default_value !== null || $field_is_required ? '' : '?') . $native_type;
        }

        return $result;
    }

    private function getTypeForDocBlock(
        string $native_type,
        $default_value,
        bool $field_is_required
    ): string
    {
        if ($native_type === 'mixed') {
            return $native_type;
        }

        return $native_type . ($default_value !== null || $field_is_required ? '' : '|null');
    }

    /**
     * Build getter for generated field.
     *
     * @param string $field_name
     * @param string $caster
     * @param string $indent
     * @param array  $result
     */
    private function buildGeneratedFieldGetter($field_name, $caster, $indent, array &$result)
    {
        $short_getter = null;

        switch ($caster) {
            case ValueCasterInterface::CAST_INT:
                $return_type = 'int';
                break;
            case ValueCasterInterface::CAST_FLOAT:
                $return_type = 'float';
                break;
            case ValueCasterInterface::CAST_BOOL:
                $return_type = 'bool';
                break;
            case ValueCasterInterface::CAST_DATE:
                $return_type = '\\' . DateValueInterface::class;
                break;
            case ValueCasterInterface::CAST_DATETIME:
                $return_type = '\\' . DateTimeValueInterface::class;
                break;
            case ValueCasterInterface::CAST_JSON:
                $return_type = 'mixed';
                break;
            default:
                $return_type = 'string';
        }

        if ($this->useShortGetterName($field_name)) {
            $short_getter = $this->getShortGetterName($field_name);

            $lines[] = '';
            $lines[] = '/**';
            $lines[] = ' * Return value of ' . $field_name . ' field.';
            $lines[] = ' *';
            $lines[] = ' * @return ' . $return_type;
            $lines[] = ' */';
            $lines[] = 'public function ' . $short_getter . '();';
        }

        $lines[] = '';
        $lines[] = '/**';
        $lines[] = ' * Return value of ' . $field_name . ' field.';
        $lines[] = ' *';
        $lines[] = ' * @return ' . $return_type;

        if ($short_getter && $this->getStructure()->getConfig('deprecate_long_bool_field_getter')) {
            $lines[] = " * @deprecated use $short_getter()";
        }

        $lines[] = ' */';
        $lines[] = 'public function ' . $this->getGetterName($field_name) . '();';

        foreach ($lines as $line) {
            $result[] = $line ? $indent . $line : '';
        }
    }

    /**
     * Return true if we should use a short getter name.
     *
     * @param  string $field_name
     * @return bool
     */
    private function useShortGetterName($field_name)
    {
        return substr($field_name, 0, 3) === 'is_' || in_array(substr($field_name, 0, 4), ['has_', 'had_', 'was_']) || in_array(substr($field_name, 0, 5), ['were_', 'have_']);
    }

    /**
     * @var array
     */
    private $getter_names = [];

    /**
     * @var array
     */
    private $setter_names = [];

    private function getGetterName(string $field_name): string
    {
        if (empty($this->getter_names[$field_name])) {
            $camelized_field_name = Inflector::classify($field_name);

            $this->getter_names[$field_name] = "get{$camelized_field_name}";
            $this->setter_names[$field_name] = "set{$camelized_field_name}";
        }

        return $this->getter_names[$field_name];
    }

    private function getShortGetterName(string $field_name): string
    {
        return lcfirst(Inflector::classify($field_name));
    }

    private function getSetterName(string $field_name): string
    {
        if (empty($this->setter_names[$field_name])) {
            $camelized_field_name = Inflector::classify($field_name);

            $this->getter_names[$field_name] = "get{$camelized_field_name}";
            $this->setter_names[$field_name] = "set{$camelized_field_name}";
        }

        return $this->setter_names[$field_name];
    }

    private function getModifierName($field_name): string
    {
        return 'modify' . Inflector::classify($field_name);
    }
}
