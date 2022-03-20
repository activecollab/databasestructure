<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\EntityBuilder;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\Field\Composite\CompositeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonFieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarFieldWithDefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\DateValue\DateValueInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

class BaseTypeInterfaceBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $base_interface_name = sprintf(
            '%sInterface',
            $this->getInflector()->classify($this->getInflector()->singularize($type->getName()))
        );
        $base_interface_extends = '\\' . ltrim($type->getBaseEntityInterfaceExtends(), '\\');

        $base_interface_build_path = $this->getBuildPath()
            ? sprintf("%s/Base/%s.php", $this->getBuildPath(), $base_interface_name)
            : null;

        $result = $this->openPhpFile();

        $base_interface_namespace = $this->getStructure()->getNamespace()
            ? sprintf('%s\\Base', $this->getStructure()->getNamespace())
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

        $this->triggerEvent(
            'on_interface_built',
            [
                $base_interface_name,
                $this->writeOrEval($base_interface_build_path, $result),
            ]
        );
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

    private function buildFieldGetterAndSetter(
        ScalarField $field,
        string $indent,
        array &$result
    ): void
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
            $lines[] = 'public function ' . $this->getSetterName($field->getName()) . '(' . $type_for_executable_code . ($type_for_executable_code ? ' ' : '') . '$value)' . ';';

            if ($field instanceof JsonFieldInterface) {
                $lines[] = '';
                $lines[] = '/**';
                $lines[] = ' * Modify value of ' . $field->getName() . ' field.';
                $lines[] = ' *';
                $lines[] = ' * @return $this';
                $lines[] = ' */';
                $lines[] = 'public function ' . $this->getModifierName($field->getName()) . '(callable $callback, bool $force_array = false)' . ';';
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
            $camelized_field_name = $this->getInflector()->classify($field_name);

            $this->getter_names[$field_name] = "get{$camelized_field_name}";
            $this->setter_names[$field_name] = "set{$camelized_field_name}";
        }

        return $this->getter_names[$field_name];
    }

    private function getShortGetterName(string $field_name): string
    {
        return lcfirst($this->getInflector()->classify($field_name));
    }

    private function getSetterName(string $field_name): string
    {
        if (empty($this->setter_names[$field_name])) {
            $camelized_field_name = $this->getInflector()->classify($field_name);

            $this->getter_names[$field_name] = "get{$camelized_field_name}";
            $this->setter_names[$field_name] = "set{$camelized_field_name}";
        }

        return $this->setter_names[$field_name];
    }

    private function getModifierName($field_name): string
    {
        return 'modify' . $this->getInflector()->classify($field_name);
    }

    private ?Inflector $inflector = null;

    private function getInflector(): Inflector
    {
        if ($this->inflector === null) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
