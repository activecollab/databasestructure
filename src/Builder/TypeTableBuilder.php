<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTimeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractorInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonFieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarFieldWithDefaultValue;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeTableBuilder extends DatabaseBuilder implements FileSystemBuilderInterface
{
    use StructureSql;

    /**
     * Build path. If empty, class will be built to memory.
     */
    private ?string $build_path = null;

    public function getBuildPath(): ?string
    {
        return $this->build_path;
    }

    public function setBuildPath(?string $value): FileSystemBuilderInterface
    {
        $this->build_path = $value;

        return $this;
    }

    public function preBuild(): void
    {
        $structure_sql_path = $this->getStructureSqlPath();
        $initial_data_sql_path = $this->getInitialDataSqlPath();

        if ($structure_sql_path && $initial_data_sql_path) {
            $sql_dir = dirname($structure_sql_path);

            if (!is_dir($sql_dir)) {
                umask();
                mkdir($sql_dir);
            }

            file_put_contents($structure_sql_path, '');
            $this->triggerEvent('on_structure_sql_built', [$structure_sql_path]);

            file_put_contents($initial_data_sql_path, '');
            $this->triggerEvent('on_initial_data_sql_built', [$initial_data_sql_path]);
        }
    }

    public function buildType(TypeInterface $type): void
    {
        if ($this->getConnection()) {
            $create_table_statement = $this->prepareCreateTableStatement($type);

            $this->appendToStructureSql($create_table_statement, 'Create ' . $this->getConnection()->escapeTableName($type->getName()) . ' table');

            if ($this->getConnection()->tableExists($type->getName())) {
                $this->triggerEvent('on_table_exists', [$type->getName()]);
            } else {
                $this->getConnection()->execute($create_table_statement);
                $this->triggerEvent('on_table_created', [$type->getName()]);
            }

            foreach ($type->getAssociations() as $association) {
                if ($association instanceof HasAndBelongsToManyAssociation) {
                    $target_type = $this->getStructure()->getType($association->getTargetTypeName());

                    $connection_table = $this->getConnectionTableName($type, $target_type);

                    $create_table_statement = $this->prepareConnectionCreateTableStatement($type, $this->getStructure()->getType($association->getTargetTypeName()), $association);
                    $this->appendToStructureSql($create_table_statement, 'Create ' . $this->getConnection()->escapeTableName($connection_table) . ' table');

                    if ($this->getConnection()->tableExists($connection_table)) {
                        $this->triggerEvent('on_table_exists', [$connection_table]);
                    } else {
                        $this->getConnection()->execute($create_table_statement);
                        $this->triggerEvent('on_table_created', [$connection_table]);
                    }
                }
            }
        }
    }

    /**
     * Prepare CREATE TABLE statement for the given type.
     */
    public function prepareCreateTableStatement(TypeInterface $type): string
    {
        $result = [];

        $result[] = 'CREATE TABLE IF NOT EXISTS ' . $this->getConnection()->escapeTableName($type->getName()) . ' (';

        $generated_field_indexes = [];

        foreach ($type->getAllFields() as $field) {
            if ($field instanceof ScalarField) {
                $result[] = '    ' . $this->prepareFieldStatement($field) . ',';
            }

            if ($field instanceof JsonFieldInterface) {
                foreach ($field->getValueExtractors() as $value_extractor) {
                    $result[] = '    ' . $this->prepareGeneratedFieldStatement($field, $value_extractor) . ',';

                    if ($value_extractor->getAddIndex()) {
                        $generated_field_indexes[] = new Index($value_extractor->getFieldName());
                    }
                }
            }
        }

        $indexes = $type->getAllIndexes();

        if (!empty($generated_field_indexes)) {
            $indexes = array_merge($indexes, $generated_field_indexes);
        }

        foreach ($indexes as $index) {
            $result[] = '    ' . $this->prepareIndexStatement($index) . ',';
        }

        $last_line = count($result) - 1;
        $result[$last_line] = rtrim($result[$last_line], ',');

        $result[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return implode("\n", $result);
    }

    /**
     * Prepare field statement based on the field settings.
     */
    private function prepareFieldStatement(ScalarField $field): string
    {
        $result = sprintf(
            '%s %s',
            $this->getConnection()->escapeFieldName($field->getName()),
            $field->getSqlTypeDefinition($this->getConnection())
        );

        if ($field instanceof DefaultValueInterface && $field->getDefaultValue() !== null) {
            $result .= ' NOT NULL';
        }

        if ($this->hasDefaultValue($field)) {
            $result .= ' DEFAULT ' . $this->prepareDefaultValue($field);
        }

        return $result;
    }

    private function hasDefaultValue(FieldInterface $field): bool
    {
        if ($field instanceof IntegerField && $field->getName() == 'id') {
            return false;
        }

        if (!$field instanceof DefaultValueInterface) {
            return false;
        }

        return true;
    }

    /**
     * Prepare default value.
     */
    public function prepareDefaultValue(ScalarFieldWithDefaultValue $field): string
    {
        $default_value = $field->getDefaultValue();

        if ($default_value === null) {
            return 'NULL';
        }

        if ($field instanceof DateField || $field instanceof DateTimeField) {
            $timestamp = is_int($default_value) ? $default_value : strtotime($default_value);

            if ($field instanceof DateTimeField) {
                return $this->getConnection()->escapeValue(date('Y-m-d H:i:s', $timestamp));
            }

            return $this->getConnection()->escapeValue(date('Y-m-d', $timestamp));
        }

        return $this->getConnection()->escapeValue($default_value);
    }

    /**
     * Prepare generated field statement.
     */
    public function prepareGeneratedFieldStatement(
        FieldInterface $source_field,
        ValueExtractorInterface $extractor
    ): string
    {
        $generated_field_name = $this->getConnection()->escapeFieldName($extractor->getFieldName());

        $field_type = match ($extractor->getValueCaster()) {
            ValueCasterInterface::CAST_INT => 'INT',
            ValueCasterInterface::CAST_FLOAT => 'DECIMAL(12, 2)',
            ValueCasterInterface::CAST_BOOL => 'TINYINT(1) UNSIGNED',
            ValueCasterInterface::CAST_DATE => 'DATE',
            ValueCasterInterface::CAST_DATETIME => 'DATETIME',
            ValueCasterInterface::CAST_JSON => 'JSON',
            default => 'VARCHAR(191)',
        };

        $expression = $this->prepareGeneratedFieldExpression(
            $this->getConnection()->escapeFieldName($source_field->getName()),
            var_export($extractor->getExpression(), true),
            $extractor->getValueCaster(),
            $this->getConnection()->escapeValue($extractor->getDefaultValue())
        );
        $storage = $extractor->getStoreValue() ? 'STORED' : 'VIRTUAL';

        return trim("$generated_field_name $field_type AS ($expression) $storage");
    }

    /**
     * Prepare extraction statement based on expression.
     */
    private function prepareGeneratedFieldExpression(
        string $escaped_field_name,
        string $escaped_expression,
        string $caster,
        mixed $escaped_default_value
    ): string
    {
        $value_extractor_expression = "JSON_UNQUOTE(JSON_EXTRACT({$escaped_field_name}, {$escaped_expression}))";

        return match ($caster) {
            ValueCasterInterface::CAST_BOOL => "IF({$value_extractor_expression} IS NULL, $escaped_default_value, IF({$value_extractor_expression} = 'true' OR ({$value_extractor_expression} REGEXP '^-?[0-9]+$' AND CAST({$value_extractor_expression} AS SIGNED) != 0), 1, 0))",
            ValueCasterInterface::CAST_DATE => "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS DATE))",
            ValueCasterInterface::CAST_DATETIME => "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS DATETIME))",
            ValueCasterInterface::CAST_INT => "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS SIGNED INTEGER))",
            ValueCasterInterface::CAST_FLOAT => "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS DECIMAL(12, 2)))",
            default => "IF({$value_extractor_expression} IS NULL, $escaped_default_value, {$value_extractor_expression})",
        };
    }

    /**
     * Prepare index statement.
     */
    public function prepareIndexStatement(IndexInterface $index): string
    {
        $result = match ($index->getIndexType()) {
            IndexInterface::PRIMARY => 'PRIMARY KEY',
            IndexInterface::UNIQUE => 'UNIQUE ' . $this->getConnection()->escapeFieldName($index->getName()),
            IndexInterface::FULLTEXT => 'FULLTEXT ' . $this->getConnection()->escapeFieldName($index->getName()),
            default => 'INDEX ' . $this->getConnection()->escapeFieldName($index->getName()),
        };

        return $result . ' (' . implode(', ', array_map(function ($field_name) {
            return $this->getConnection()->escapeFieldName($field_name);
        }, $index->getFields())) . ')';
    }

    /**
     * Return name of the connection that will be created for has and belongs to many association.
     */
    private function getConnectionTableName(TypeInterface $source, TypeInterface $target): string
    {
        return $source->getName() . '_' . $target->getName();
    }

    /**
     * Prepare create connection table statement.
     */
    public function prepareConnectionCreateTableStatement(
        TypeInterface $source,
        TypeInterface $target,
        HasAndBelongsToManyAssociation $association
    ): string
    {
        $result = [];

        $result[] = 'CREATE TABLE IF NOT EXISTS ' . $this->getConnection()->escapeTableName($association->getConnectionTableName()) . ' (';

        $left_field_name = $association->getLeftFieldName();
        $right_field_name = $association->getRightFieldName();

        $left_field = (new IntegerField($left_field_name, 0))
            ->unsigned()
            ->size($source->getIdField()->getSize());
        $right_field = (new IntegerField($right_field_name, 0))
            ->unsigned()
            ->size($target->getIdField()->getSize());

        $result[] = '    ' . $this->prepareFieldStatement($left_field) . ',';
        $result[] = '    ' . $this->prepareFieldStatement($right_field) . ',';
        $result[] = '    ' . $this->prepareIndexStatement(new Index('PRIMARY', [$left_field->getName(), $right_field->getName()], IndexInterface::PRIMARY)) . ',';
        $result[] = '    ' . $this->prepareIndexStatement(new Index($right_field->getName()));

        $result[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return implode("\n", $result);
    }
}
