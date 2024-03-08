<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\EntityBuilder;

use ActiveCollab\DatabaseConnection\Record\ValueCaster;
use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Association\InjectFieldsInsterface;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\Field\Composite\CompositeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonFieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarFieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarFieldWithDefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\DateValue\DateValueInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

class BaseTypeClassBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $base_class_name = $type->getEntityClassName();
        $interface_name = $type->getEntityInterfaceName();
        $base_class_extends_fqn = ltrim($type->getBaseEntityClassExtends(), '\\');
        $base_class_extends_alias = $this->getBaseClassExtendsAlias($base_class_extends_fqn);

        $base_class_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Base/$base_class_name.php"
            : null;

        $result = $this->openPhpFile();

        $base_class_namespace = $this->getStructure()->getNamespace()
            ? $this->getStructure()->getNamespace() . '\\Base'
            : 'Base';

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';

        $result[] = sprintf(
            'use %s\\%s;',
            $this->getStructure()->getNamespace(),
            $interface_name
        );
        $result[] = sprintf('use %s as %s;', $base_class_extends_fqn, $base_class_extends_alias);
        $result[] = '';

        $this->buildBaseClassDocBlockProperties($result);

        $traits = [];

        foreach ($type->getTraits() as $implementations) {
            if (!empty($implementations)) {
                foreach ($implementations as $implementation) {
                    $traits[] = '\\' . ltrim($implementation, '\\');
                }
            }
        }

        $this->buildClassDeclaration($base_class_name, $base_class_extends_alias, $interface_name, '', $result);

        $result[] = '{';

        $this->buildClassTraits($type, $traits, '    ', $result);

        $result[] = '    /**';
        $result[] = '     * Name of the table where records are stored.';
        $result[] = '     */';
        $result[] = '    protected string $table_name = ' . var_export($type->getTableName(), true) . ';';

        $fields = $type->getAllFields();

        $this->buildFields($type->getTableName(), $fields, '    ', $result);
        $this->buildGeneratedFields(array_keys($type->getGeneratedFields()), '    ', $result);

        if (count($type->getProtectedFields())) {
            $result[] = '';
            $result[] = '    /**';
            $result[] = '     * List of protected fields.';
            $result[] = '     */';
            $result[] = '    protected array $protected_fields = [' . implode(', ', array_map(function ($field) {
                return var_export($field, true);
            }, $type->getProtectedFields())) . '];';
        }

        if ($type->getOrderBy() != ['id']) {
            $result[] = '';
            $result[] = '    /**';
            $result[] = '     * @var string[]';
            $result[] = '     */';
            $result[] = '    protected array $order_by = [' . implode(', ', array_map(function ($value) {
                return var_export($value, true);
            }, $type->getOrderBy())) . '];';
        }

        $this->buildConfigureMethod(
            $type->getFields(),
            $type->getGeneratedFields(),
            '    ',
            $result
        );

        $this->buildAssociatedEntitiesManagers($type, '    ', $result);
        $this->buildSetAttributes($type, '    ', $result);

        foreach ($type->getAssociations() as $association) {
            $association->buildClassPropertiesAndMethods(
                $this->getStructure(),
                $type,
                $this->getStructure()->getType($association->getTargetTypeName()),
                $result
            );
        }

        $generated_fields = [];

        foreach ($fields as $field) {
            if ($field instanceof GeneratedInterface && $field->isGenerated()) {
                $generated_fields[$field->getName()] = $field;
                continue;
            }

            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel() && $field->getName() != 'id') {
                $this->buildFieldGetterAndSetter($field, '    ', $result);
            }
        }

        foreach ($generated_fields as $generated_field) {
            $this->buildGeneratedFieldGetter(
                $generated_field,
                $generated_field->getName(),
                '',
                '    ',
                $result
            );
        }

        foreach ($type->getGeneratedFields() as $field_name => $caster) {
            if (array_key_exists($field_name, $generated_fields)) {
                continue;
            }

            $this->buildGeneratedFieldGetter(null, $field_name, $caster, '    ', $result);
        }

        $build_custom_get_field_value = false;

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel() && !empty($field->getDeserializingCode('raw_value'))) {
                $build_custom_get_field_value = true;
                break;
            }
        }

        if ($build_custom_get_field_value) {
            $result[] = '';
            $result[] = '    public function getFieldValue(string $field, mixed $default = null): mixed';
            $result[] = '    {';
            $result[] = '        $value = parent::getFieldValue($field, $default);';
            $result[] = '';
            $result[] = '        if ($value === null) {';
            $result[] = '            return null;';
            $result[] = '        }';
            $result[] = '';
            $result[] = '        switch ($field) {';

            foreach ($fields as $field) {
                if ($field instanceof ScalarField && $field->getShouldBeAddedToModel() && !empty($field->getDeserializingCode('value'))) {
                    $result[] = '            case ' . var_export($field->getName(), true) . ':';
                    $result[] = '                return ' . $field->getDeserializingCode('value') . ';';
                }
            }

            $result[] = '        }';
            $result[] = '';
            $result[] = '        return $value;';
            $result[] = '    }';
        }

        $result[] = '';
        $result[] = '    public function setFieldValue(string $field, mixed $value): static';
        $result[] = '    {';
        $result[] = '        if ($value === null) {';
        $result[] = '            parent::setFieldValue($field, null);';
        $result[] = '            return $this;';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        switch ($field) {';

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
                $result[] = '            case ' . var_export($casted_field_name, true) . ':';
            }

            $result[] = '                return parent::setFieldValue($field, ' . $caster_code . ');';
        }

        $result[] = '            default:';
        $result[] = '                if ($this->isLoading()) {';
        $result[] = '                    return parent::setFieldValue($field, $value);';
        $result[] = '                }';
        $result[] = '';
        $result[] = '                if ($this->isGeneratedField($field)) {';
        $result[] = '                    throw new \\LogicException("Generated field $field cannot be set by directly assigning a value");';
        $result[] = '                }';
        $result[] = '';
        $result[] = '                throw new \\InvalidArgumentException("Field $field does not exist in this table");';
        $result[] = '        }';
        $result[] = '    }';

        $this->buildJsonSerialize($type->getSerialize(), '    ', $result);
        $this->buildCompositeFieldMethods($type->getFields(), '    ', $result);
        $this->buildValidate($type->getFields(), $type->getAssociations(), '    ', $result);

        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_class_built',
            [
                $base_class_name,
                $this->writeOrEval($base_class_build_path, $result),
            ]
        );
    }

    private function buildBaseClassDocBlockProperties(array &$result): void
    {
        $base_class_doc_block_properties = $this->getStructure()->getConfig('base_class_doc_block_properties');

        if (is_array($base_class_doc_block_properties) && !empty($base_class_doc_block_properties)) {
            $result[] = '/**';

            foreach ($base_class_doc_block_properties as $property => $property_type) {
                $result[] = " * @property {$property_type} \${$property}";
            }

            $result[] = ' */';
        }
    }

    public function buildClassDeclaration(
        string $base_class_name,
        string $base_class_extends,
        string $interface_name,
        string $indent,
        array &$result
    ): void
    {
        $result[] = sprintf(
            '%sabstract class %s extends %s implements %s',
            $indent,
            $base_class_name,
            $base_class_extends,
            $interface_name
        );
    }

    private function buildClassTraits(TypeInterface $type, array $traits, string $indent, array &$result): void
    {
        if (count($traits)) {
            $result[] = $indent . 'use';

            foreach ($traits as $trait) {
                $result[] = $indent . '    ' . $trait . ',';
            }

            $this->removeCommaFromLastLine($result);

            $trait_tweaks_count = count($type->getTraitTweaks());

            if ($trait_tweaks_count) {
                $result[] = $indent . '    {';

                for ($i = 0; $i < $trait_tweaks_count - 1; ++$i) {
                    $result[] = $indent . '        ' . $type->getTraitTweaks()[$i] . ($i < $trait_tweaks_count - 2 ? ',' : '');
                }

                $result[] = $indent . '    }';
            } else {
                $result[count($result) - 1] .= ';';
            }

            $result[] = '';
        }
    }

    private function removeCommaFromLastLine(array &$result): void
    {
        $last_line_num = count($result) - 1;

        if ($last_line_num >= 0) {
            $result[$last_line_num] = rtrim($result[$last_line_num], ',');
        }
    }

    /**
     * Build field definitions.
     *
     * @param string           $table_name
     * @param FieldInterface[] $fields
     * @param string           $indent
     * @param array            $result
     */
    private function buildFields(
        string $table_name,
        array $fields,
        string $indent,
        array &$result
    )
    {
        $stringified_field_names = [];
        $stringified_sql_read_statements = [];
        $fields_with_default_value = [];

        foreach ($fields as $field) {
            if ($field instanceof ScalarField && $field->getShouldBeAddedToModel()) {
                $stringified_field_names[] = var_export($field->getName(), true);
                $stringified_sql_read_statements[] = var_export(
                    $field->getSqlReadStatement($table_name),
                    true
                );

                if ($field->getName() != 'id'
                    && ($field instanceof DefaultValueInterface && $field->getDefaultValue() !== null)) {
                    $fields_with_default_value[$field->getName()] = $field->getDefaultValue();
                }
            }
        }

        $result[] = '';
        $result[] = $indent . '/**';
        $result[] = $indent . ' * Table fields that are managed by this entity.';
        $result[] = $indent . ' */';
        $result[] = $indent . 'protected array $entity_fields = [';

        foreach ($stringified_field_names as $stringified_field_name) {
            $result[] = $indent . '    ' . $stringified_field_name . ',';
        }

        $result[] = $indent . '];';

        $result[] = '';
        $result[] = $indent . '/**';
        $result[] = $indent . ' * Table fields prepared for SELECT SQL query.';
        $result[] = $indent . ' */';
        $result[] = $indent . 'protected array $sql_read_statements = [';

        foreach ($stringified_sql_read_statements as $stringified_sql_read_statement) {
            $result[] = $indent . '    ' . $stringified_sql_read_statement . ',';
        }

        $result[] = $indent . '];';

        if (count($fields_with_default_value)) {
            $result[] = '';
            $result[] = $indent . '/**';
            $result[] = $indent . ' * List of default field values.';
            $result[] = $indent . ' */';
            $result[] = $indent . 'protected array $default_entity_field_values = [';

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
    private function buildGeneratedFields(
        array $generated_field_names,
        string $indent,
        array &$result
    ): void
    {
        $result[] = '';
        $result[] = $indent . '/**';
        $result[] = $indent . ' * Generated fields that are loaded, but not managed by the entity.';
        $result[] = $indent . ' */';
        $result[] = $indent . 'protected array $generated_entity_fields = [';

        foreach ($generated_field_names as $generated_field_name) {
            $result[] = sprintf('%s    %s,',
                $indent,
                var_export($generated_field_name, true)
            );
        }

        $result[] = $indent . '];';
    }

    /**
     * @param FieldInterface[] $fields
     */
    private function buildConfigureMethod(
        array $fields,
        array $generated_fields,
        string $indent,
        array &$result
    ): void
    {
        $field_casters = [];

        foreach ($fields as $field) {
            if ($field instanceof ScalarField) {
                $field_casters[$field->getName()] = $field->getValueCaster();
            }
        }

        if (!empty($generated_fields)) {
            $field_casters = array_merge($field_casters, $generated_fields);
        }

        if (!empty($field_casters)) {
            $result[] = '';
            $result[] = $indent . 'protected function configure(): void';
            $result[] = $indent . '{';
            $result[] = $indent . '    $this->setGeneratedFieldsValueCaster(';
            $result[] = $indent . '        new \\' . ValueCaster::class . '(';
            $result[] = $indent . '            [';

            foreach ($field_casters as $field_name => $caster) {
                $full_caster = match ($caster) {
                    ValueCasterInterface::CAST_INT => '\\' . ValueCasterInterface::class . '::CAST_INT',
                    ValueCasterInterface::CAST_FLOAT => '\\' . ValueCasterInterface::class . '::CAST_FLOAT',
                    ValueCasterInterface::CAST_BOOL => '\\' . ValueCasterInterface::class . '::CAST_BOOL',
                    ValueCasterInterface::CAST_DATE => '\\' . ValueCasterInterface::class . '::CAST_DATE',
                    ValueCasterInterface::CAST_DATETIME => '\\' . ValueCasterInterface::class . '::CAST_DATETIME',
                    ValueCasterInterface::CAST_JSON => '\\' . ValueCasterInterface::class . '::CAST_JSON',
                    ValueCasterInterface::CAST_SPATIAL => '\\' . ValueCasterInterface::class . '::CAST_SPATIAL',
                    default => '\\' . ValueCasterInterface::class . '::CAST_STRING',
                };

                $result[] = sprintf(
                    '%s                %s => %s,',
                    $indent,
                    var_export($field_name, true),
                    $full_caster
                );
            }

            $result[] = $indent . '            ]';
            $result[] = $indent . '        )';
            $result[] = $indent . '    );';
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
            $result[] = $indent . '/**';
            $result[] = $indent . ' * {@inheritdoc}';
            $result[] = $indent . ' */';
            $result[] = $indent . 'private $associated_entities_managers;';
        }

        $result[] = '';
        $result[] = $indent . '/**';
        $result[] = $indent . ' * {@inheritdoc}';
        $result[] = $indent . ' */';
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
            $result[] = $indent . 'public function setAttribute(string $attribute, mixed $value): static';
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

    /**
     * @param FieldInterface[] $fields
     * @param string           $indent
     * @param array            $result
     */
    private function buildCompositeFieldMethods($fields, $indent, array &$result)
    {
        foreach ($fields as $field) {
            if ($field instanceof CompositeField) {
                $field->getBaseClassMethods($indent, $result);
            }
        }
    }

    private function buildFieldGetterAndSetter(
        ScalarField $field,
        string $indent,
        array &$result,
    ): void
    {
        $setter_access_level = $field->getProtectSetter() ? 'protected' : 'public';

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

            $lines[] = '';
            $lines[] = '/**';
            $lines[] = ' * Return value of ' . $field->getName() . ' field.';
            $lines[] = ' *';
            $lines[] = ' * @return ' . $type_for_doc_block;
            $lines[] = ' */';
            $lines[] = 'public function ' . $short_getter . '()' . ($type_for_executable_code ? ': ' : '') . $type_for_executable_code;
            $lines[] = '{';

            if ($field->isRequired()) {
                $lines[] = '    $field_value = $this->getFieldValue(' . var_export($field->getName(), true) . ');';
                $lines[] = '';
                $lines[] = '    if ($field_value === null) {';
                $lines[] = "        throw new \\LogicException(\"Value of '{$field->getName()}' should not be accessed prior to being set.\");";
                $lines[] = '    }';
                $lines[] = '';
                $lines[] = '    return $field_value;';
            } else {
                $lines[] = '    return $this->getFieldValue(' . var_export($field->getName(), true) . ');';
            }

            $lines[] = '}';
        }

        $lines[] = '';
        $lines[] = '/**';
        $lines[] = ' * Return value of ' . $field->getName() . ' field.';
        $lines[] = ' *';
        $lines[] = ' * @return ' . $type_for_doc_block;

        if ($short_getter && $this->getStructure()->getConfig('deprecate_long_bool_field_getter')) {
            $lines[] = " * @deprecated use $short_getter()";
        }

        $lines[] = ' */';
        $lines[] = 'public function ' . $this->getGetterName($field->getName()) . '()' . ($type_for_executable_code ? ': ' : '') . $type_for_executable_code;
        $lines[] = '{';

        if ($field->isRequired()) {
            $lines[] = '    $field_value = $this->getFieldValue(' . var_export($field->getName(), true) . ');';
            $lines[] = '';
            $lines[] = '    if ($field_value === null) {';
            $lines[] = "        throw new \\LogicException(\"Value of '{$field->getName()}' should not be accessed prior to being set.\");";
            $lines[] = '    }';
            $lines[] = '';
            $lines[] = '    return $field_value;';
        } else {
            $lines[] = '    return $this->getFieldValue(' . var_export($field->getName(), true) . ');';
        }

        $lines[] = '}';
        $lines[] = '';
        $lines[] = '/**';
        $lines[] = ' * Set value of ' . $field->getName() . ' field.';
        $lines[] = ' *';
        $lines[] = ' * @param  ' . str_pad($type_for_doc_block, 5, ' ', STR_PAD_RIGHT) . ' $value';
        $lines[] = ' * @return $this';
        $lines[] = ' */';
        $lines[] = $setter_access_level . ' function ' . $this->getSetterName($field->getName()) . '(' . $type_for_executable_code . ($type_for_executable_code ? ' ' : '') . '$value)';
        $lines[] = '{';
        $lines[] = '    $this->setFieldValue(' . var_export($field->getName(), true) . ', $value);';
        $lines[] = '';
        $lines[] = '    return $this;';
        $lines[] = '}';

        if ($field instanceof JsonFieldInterface) {
            $this->buildModifier(
                $field,
                $this->getGetterName($field->getName()),
                $this->getSetterName($field->getName()),
                $setter_access_level,
                $lines
            );
        }

        foreach ($lines as $line) {
            $result[] = $line ? $indent . $line : '';
        }
    }

    private function buildModifier(JsonFieldInterface $field, string $getter_name, string $setter_name, string $setter_access_level, array &$lines): void
    {
        $lines[] = '';
        $lines[] = '/**';
        $lines[] = ' * Modify value of ' . $field->getName() . ' field.';
        $lines[] = ' *';
        $lines[] = ' * @param  callable $callback';
        $lines[] = ' * @param  bool     $force_array';
        $lines[] = ' * @return $this';
        $lines[] = ' */';
        $lines[] = $setter_access_level . ' function ' . $this->getModifierName($field->getName()) . '(callable $callback, bool $force_array = false)';
        $lines[] = '{';
        $lines[] = '    $value = $this->' . $getter_name . '();';
        $lines[] = '';
        $lines[] = '    if ($force_array && $value === null) {';
        $lines[] = '        $value = [];';
        $lines[] = '    }';
        $lines[] = '';
        $lines[] = '    $modified_value = call_user_func($callback, $value);';
        $lines[] = '';
        $lines[] = '    if (!is_array($modified_value) && !is_null($modified_value)) {';
        $lines[] = "        throw new \\LogicException('Modifier callback should return array or NULL.');";
        $lines[] = '    }';
        $lines[] = '';
        $lines[] = '    $this->' . $setter_name . '($modified_value);';
        $lines[] = '';
        $lines[] = '    return $this;';
        $lines[] = '}';
    }

    private function getTypeForExecutableCode(string $native_type, $default_value, bool $field_is_required): string
    {
        $result = '';

        if ($native_type != 'mixed') {
            $result = ($default_value !== null || $field_is_required ? '' : '?') . $native_type;
        }

        return $result;
    }

    private function getTypeForDocBlock(string $native_type, $default_value, bool $field_is_required): string
    {
        if ($native_type === 'mixed') {
            return $native_type;
        }

        return $native_type . ($default_value !== null || $field_is_required ? '' : '|null');
    }

    /**
     * Build getter for generated field.
     */
    private function buildGeneratedFieldGetter(
        ?FieldInterface $field,
        string $field_name,
        string $caster,
        string $indent,
        array &$result
    ): void
    {
        $short_getter = null;

        if ($field) {
            $default_value = $field instanceof ScalarFieldWithDefaultValueInterface ? $field->getDefaultValue() : null;

            $type_for_executable_code = $this->getTypeForExecutableCode(
                $field->getNativeType(),
                $default_value,
                $field->isRequired()
            );
        } else {
            $type_for_executable_code = '?' . match ($caster) {
                ValueCasterInterface::CAST_INT => 'int',
                ValueCasterInterface::CAST_FLOAT => 'float',
                ValueCasterInterface::CAST_BOOL => 'bool',
                ValueCasterInterface::CAST_DATE => '\\' . DateValueInterface::class,
                ValueCasterInterface::CAST_DATETIME => '\\' . DateTimeValueInterface::class,
                ValueCasterInterface::CAST_JSON => 'mixed',
                default => 'string',
            };
        }

        if ($this->useShortGetterName($field_name)) {
            $short_getter = $this->getShortGetterName($field_name);

            $lines[] = '';
            $lines[] = '/**';
            $lines[] = ' * Return value of ' . $field_name . ' field.';
            $lines[] = ' */';
            $lines[] = 'public function ' . $short_getter . '()' . ($type_for_executable_code ? ': ' : '') . $type_for_executable_code;
            $lines[] = '{';
            $lines[] = '    return $this->getFieldValue(' . var_export($field_name, true) . ');';
            $lines[] = '}';
        }

        $lines[] = '';
        $lines[] = '/**';
        $lines[] = ' * Return value of ' . $field_name . ' field.';

        if ($short_getter && $this->getStructure()->getConfig('deprecate_long_bool_field_getter')) {
            $lines[] = " * @deprecated use $short_getter()";
        }

        $lines[] = ' */';
        $lines[] = 'public function ' . $this->getGetterName($field_name) . '()' . ($type_for_executable_code ? ': ' : '') . $type_for_executable_code;
        $lines[] = '{';
        $lines[] = '    return $this->getFieldValue(' . var_export($field_name, true) . ');';
        $lines[] = '}';
        $lines[] = '';

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
     * Build JSON serialize method, if we need to serialize extra fields.
     *
     * @param array  $serialize
     * @param string $indent
     * @param array  $result
     */
    private function buildJsonSerialize(array $serialize, $indent, array &$result)
    {
        if (count($serialize)) {
            $result[] = '';
            $result[] = $indent . 'public function jsonSerialize(): mixed';
            $result[] = $indent . '{';
            $result[] = $indent . '    return array_merge(parent::jsonSerialize(), [';

            foreach ($serialize as $field) {
                $result[] = $indent . '        ' . var_export($field, true) . ' => $this->' . $this->getGetterName($field) . '(),';
            }

            $result[] = $indent . '    ]);';
            $result[] = $indent . '}';
        }
    }

    /**
     * @param FieldInterface[]       $fields
     * @param AssociationInterface[] $associations
     */
    private function buildValidate(
        array $fields,
        array $associations,
        string $indent,
        array &$result
    ): void
    {
        $fields_to_validate = $fields;

        foreach ($associations as $association) {
            if ($association instanceof InjectFieldsInsterface && count($association->getFields())) {
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
                        $this->buildValidatePresenceLinesForScalarField(
                            $subfield,
                            $line_indent,
                            $validator_lines
                        );
                    }
                }
            } elseif ($field instanceof ScalarField && $field->getShouldBeAddedToModel()) {
                $this->buildValidatePresenceLinesForScalarField(
                    $field,
                    $line_indent,
                    $validator_lines
                );
            }
        }

        if (!empty($validator_lines)) {
            $result[] = '';
            $result[] = $indent . '/**';
            $result[] = $indent . ' * Validate object properties before object is saved.';
            $result[] = $indent . ' */';
            $result[] = $indent . 'public function validate(\ActiveCollab\DatabaseObject\ValidatorInterface $validator): \ActiveCollab\DatabaseObject\ValidatorInterface';
            $result[] = $indent . '{';

            $result = array_merge($result, $validator_lines);

            $result[] = '';
            $result[] = $indent . '    return parent::validate($validator);';
            $result[] = $indent . '}';
        }
    }

    /**
     * Build validate lines for scalar fields.
     */
    private function buildValidatePresenceLinesForScalarField(
        ScalarFieldInterface $field,
        string $line_indent,
        array &$validator_lines
    )
    {
        if ($field->isRequired() && $field->isUnique()) {
            $validator_lines[] = $line_indent . $this->buildValidatePresenceAndUniquenessLine(
                $field->getName(),
                $field->getUniquenessContext()
            );
            return;
        }

        if ($field->isRequired()) {
            $validator_lines[] = $line_indent . $this->buildValidatePresenceLine($field->getName());
        }

        if ($field->isUnique()) {
            $validator_lines[] = $line_indent . $this->buildValidateUniquenessLine(
                $field->getName(),
                $field->getUniquenessContext()
            );
        }

        if ($field->isOnlyOne()) {
            $validator_lines[] = $line_indent . $this->buildValidateOnlyOneLine(
                $field->getName(),
                $field->getOnlyOneWithValue(),
                $field->getOnlyOneInContext()
            );
        }
    }

    /**
     * Build validator value presence line.
     */
    private function buildValidatePresenceLine(string $field_name): string
    {
        return '$validator->present(' . var_export($field_name, true) . ');';
    }

    /**
     * Build validator uniqueness line.
     */
    private function buildValidateUniquenessLine(string $field_name, array $context): string
    {
        $field_names = [
            var_export($field_name, true),
        ];

        foreach ($context as $v) {
            $field_names[] = var_export($v, true);
        }

        return '$validator->unique(' . implode(', ', $field_names) . ');';
    }

    /**
     * Build validator uniqueness line.
     */
    private function buildValidateOnlyOneLine(string $field_name, mixed $field_value, array $context): string
    {
        $validator_arguments = [
            var_export($field_name, true),
            var_export($field_value, true),
        ];

        foreach ($context as $v) {
            $validator_arguments[] = var_export($v, true);
        }

        return '$validator->onlyOne(' . implode(', ', $validator_arguments) . ');';
    }

    /**
     * Build validator uniqueness line.
     */
    private function buildValidatePresenceAndUniquenessLine(string $field_name, array $context): string
    {
        $field_names = [
            var_export($field_name, true),
        ];

        foreach ($context as $v) {
            $field_names[] = var_export($v, true);
        }

        return '$validator->presentAndUnique(' . implode(', ', $field_names) . ');';
    }

    private array $getter_names = [];
    private array $setter_names = [];

    private function getGetterName(string $field_name): string
    {
        if (empty($this->getter_names[$field_name])) {
            $classified_field_name = $this->getInflector()->classify($field_name);

            $this->getter_names[$field_name] = "get{$classified_field_name}";
            $this->setter_names[$field_name] = "set{$classified_field_name}";
        }

        return $this->getter_names[$field_name];
    }

    /**
     * Return short getter name, without get bit.
     */
    private function getShortGetterName(string $field_name): string
    {
        return lcfirst($this->getInflector()->classify($field_name));
    }

    private function getSetterName(string $field_name): string
    {
        if (empty($this->setter_names[$field_name])) {
            $classified_field_name = $this->getInflector()->classify($field_name);

            $this->getter_names[$field_name] = "get{$classified_field_name}";
            $this->setter_names[$field_name] = "set{$classified_field_name}";
        }

        return $this->setter_names[$field_name];
    }

    private function getModifierName($field_name): string
    {
        return sprintf('modify%s', $this->getInflector()->classify($field_name));
    }

    private function getBaseClassExtendsAlias(string $base_class_extends_fqn): string
    {
        $fqn_bits = explode('\\', $base_class_extends_fqn);

        return sprintf('Base%s', $fqn_bits[count($fqn_bits) - 1]);
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
