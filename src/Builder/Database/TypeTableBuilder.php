<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder\Database;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Builder\Database\DatabaseBuilder;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilderInterface;
use ActiveCollab\DatabaseStructure\Builder\Database\StructureSql;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTimeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DecimalField;
use ActiveCollab\DatabaseStructure\Field\Scalar\EnumField;
use ActiveCollab\DatabaseStructure\Field\Scalar\FloatField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractorInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonFieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\PasswordField;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarFieldWithDefaultValue;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\TextField;
use ActiveCollab\DatabaseStructure\Field\Scalar\TimeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class TypeTableBuilder extends DatabaseBuilder implements FileSystemBuilderInterface
{
    use StructureSql;

    /**
     * Build path. If empty, class will be built to memory.
     *
     * @var string
     */
    private $build_path;

    /**
     * Return build path.
     *
     * @return string
     */
    public function getBuildPath()
    {
        return $this->build_path;
    }

    /**
     * Set build path. If empty, class will be built in memory.
     *
     * @param  string $value
     * @return $this
     */
    public function &setBuildPath($value)
    {
        $this->build_path = $value;

        return $this;
    }

    /**
     * Execute prior to type build.
     */
    public function preBuild()
    {
        $structure_sql_path = $this->getStructureSqlPath();
        $initial_data_sql_path = $this->getInitialDataSqlPath();

        if ($structure_sql_path && $initial_data_sql_path) {
            $sql_dir = dirname($structure_sql_path);

            if (!is_dir($sql_dir)) {
                $old_mask = umask();
                mkdir($sql_dir);
            }

            file_put_contents($structure_sql_path, '');
            $this->triggerEvent('on_structure_sql_built', [$structure_sql_path]);

            file_put_contents($initial_data_sql_path, '');
            $this->triggerEvent('on_initial_data_sql_built', [$initial_data_sql_path]);
        }
    }

    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
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
     *
     * @param  TypeInterface $type
     * @return string
     */
    public function prepareCreateTableStatement(TypeInterface $type)
    {
        $result = [];

        $result[] = 'CREATE TABLE IF NOT EXISTS ' . $this->getConnection()->escapeTableName($type->getName()) . ' (';

        $generaterd_field_indexes = [];

        foreach ($type->getAllFields() as $field) {
            if ($field instanceof ScalarField) {
                $result[] = '    ' . $this->prepareFieldStatement($field) . ',';
            }

            if ($field instanceof JsonFieldInterface) {
                foreach ($field->getValueExtractors() as $value_extractor) {
                    $result[] = '    ' . $this->prepareGeneratedFieldStatement($field, $value_extractor) . ',';

                    if ($value_extractor->getAddIndex()) {
                        $generaterd_field_indexes[] = new Index($value_extractor->getFieldName());
                    }
                }
            }
        }

        $indexes = $type->getAllIndexes();

        if (!empty($generaterd_field_indexes)) {
            $indexes = array_merge($indexes, $generaterd_field_indexes);
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
     *
     * @param  ScalarField $field
     * @return string
     */
    private function prepareFieldStatement(ScalarField $field)
    {
        $result = $this->getConnection()->escapeFieldName($field->getName()) . ' ' . $this->prepareTypeDefinition($field);

        if ($field instanceof DefaultValueInterface && $field->getDefaultValue() !== null) {
            $result .= ' NOT NULL';
        }

        if ($this->hasDefaultValue($field)) {
            $result .= ' DEFAULT ' . $this->prepareDefaultValue($field);
        }

        return $result;
    }

    private function hasDefaultValue(FieldInterface $field)
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
     * Prepare type definition for the given field.
     *
     * @param  ScalarField $field
     * @return string
     */
    private function prepareTypeDefinition(ScalarField $field)
    {
        if ($field instanceof IntegerField) {
            switch ($field->getSize()) {
                case FieldInterface::SIZE_TINY:
                    $result = 'TINYINT';
                    break;
                case ScalarField::SIZE_SMALL:
                    $result = 'SMALLINT';
                    break;
                case FieldInterface::SIZE_MEDIUM:
                    $result = 'MEDIUMINT';
                    break;
                case FieldInterface::SIZE_BIG:
                    $result = 'BIGINT';
                    break;
                default:
                    $result = 'INT';
            }

            if ($field->isUnsigned()) {
                $result .= ' UNSIGNED';
            }

            if ($field->getName() == 'id') {
                $result .= ' AUTO_INCREMENT';
            }

            return $result;
        } elseif ($field instanceof BooleanField) {
            return 'TINYINT(1) UNSIGNED';
        } elseif ($field instanceof DateField) {
            return 'DATE';
        } elseif ($field instanceof DateTimeField) {
            return 'DATETIME';
        } elseif ($field instanceof DecimalField) {
            $result = 'DECIMAL(' . $field->getLength() . ', ' . $field->getScale() . ')';

            if ($field->isUnsigned()) {
                $result .= ' UNSIGNED';
            }

            return $result;
        } elseif ($field instanceof EnumField) {
            return 'ENUM(' . implode(',', array_map(function ($possibility) {
                return $this->getConnection()->escapeValue($possibility);
            }, $field->getPossibilities())) . ')';
        } elseif ($field instanceof FloatField) {
            $result = 'FLOAT(' . $field->getLength() . ', ' . $field->getScale() . ')';

            if ($field->isUnsigned()) {
                $result .= ' UNSIGNED';
            }

            return $result;
        } elseif ($field instanceof JsonField) {
            return 'JSON';
        } elseif ($field instanceof StringField) {
            return 'VARCHAR(' . $field->getLength() . ')';
        } elseif ($field instanceof PasswordField) {
            return 'VARCHAR(191)';
        } elseif ($field instanceof TextField) {
            switch ($field->getSize()) {
                case FieldInterface::SIZE_TINY:
                    return 'TINYTEXT';
                case ScalarField::SIZE_SMALL:
                    return 'TEXT';
                case FieldInterface::SIZE_MEDIUM:
                    return 'MEDIUMTEXT';
                default:
                    return 'LONGTEXT';
            }
        } elseif ($field instanceof TimeField) {
            return 'TIME';
        } else {
            throw new InvalidArgumentException('Field ' . get_class($field) . ' is not a support scalar field');
        }
    }

    /**
     * Prepare default value.
     *
     * @param  ScalarFieldWithDefaultValue $field
     * @return string
     */
    public function prepareDefaultValue(ScalarFieldWithDefaultValue $field)
    {
        $default_value = $field->getDefaultValue();

        if ($default_value === null) {
            return 'NULL';
        }

        if ($field instanceof DateField || $field instanceof DateTimeField) {
            $timestamp = is_int($default_value) ? $default_value : strtotime($default_value);

            if ($field instanceof DateTimeField) {
                return $this->getConnection()->escapeValue(date('Y-m-d H:i:s', $timestamp));
            } else {
                return $this->getConnection()->escapeValue(date('Y-m-d', $timestamp));
            }
        }

        return $this->getConnection()->escapeValue($default_value);
    }

    /**
     * Prpeare generated field statement.
     *
     * @param  FieldInterface          $source_field
     * @param  ValueExtractorInterface $extractor
     * @return string
     */
    public function prepareGeneratedFieldStatement(FieldInterface $source_field, ValueExtractorInterface $extractor)
    {
        $generated_field_name = $this->getConnection()->escapeFieldName($extractor->getFieldName());

        switch ($extractor->getValueCaster()) {
            case ValueCasterInterface::CAST_INT:
                $field_type = 'INT';
                break;
            case ValueCasterInterface::CAST_FLOAT:
                $field_type = 'DECIMAL(12, 2)';
                break;
            case ValueCasterInterface::CAST_BOOL:
                $field_type = 'TINYINT(1) UNSIGNED';
                break;
            case ValueCasterInterface::CAST_DATE:
                $field_type = 'DATE';
                break;
            case ValueCasterInterface::CAST_DATETIME:
                $field_type = 'DATETIME';
                break;
            case ValueCasterInterface::CAST_JSON:
                $field_type = 'JSON';
                break;
            default:
                $field_type = 'VARCHAR(191)';
        }

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
     *
     * @param  string $escaped_field_name
     * @param  string $escaped_expression
     * @param  string $caster
     * @param  mixed  $escaped_default_value
     * @return string
     */
    private function prepareGeneratedFieldExpression($escaped_field_name, $escaped_expression, $caster, $escaped_default_value)
    {
        $value_extractor_expression = "JSON_UNQUOTE(JSON_EXTRACT({$escaped_field_name}, {$escaped_expression}))";

        switch ($caster) {
            case ValueCasterInterface::CAST_BOOL:
                return "IF({$value_extractor_expression} IS NULL, $escaped_default_value, IF({$value_extractor_expression} = 'true' OR ({$value_extractor_expression} REGEXP '^-?[0-9]+$' AND CAST({$value_extractor_expression} AS SIGNED) != 0), 1, 0))";
            case ValueCasterInterface::CAST_DATE:
                return "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS DATE))";
            case ValueCasterInterface::CAST_DATETIME:
                return "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS DATETIME))";
            case ValueCasterInterface::CAST_INT:
                return "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS SIGNED INTEGER))";
            case ValueCasterInterface::CAST_FLOAT:
                return "IF({$value_extractor_expression} IS NULL, $escaped_default_value, CAST({$value_extractor_expression} AS DECIMAL(12, 2)))";
            default:
                return "IF({$value_extractor_expression} IS NULL, $escaped_default_value, {$value_extractor_expression})";
        }
    }

    /**
     * Prepare index statement.
     *
     * @param  IndexInterface $index
     * @return string
     */
    public function prepareIndexStatement(IndexInterface $index)
    {
        switch ($index->getIndexType()) {
            case IndexInterface::PRIMARY:
                $result = 'PRIMARY KEY';
                break;
            case IndexInterface::UNIQUE:
                $result = 'UNIQUE ' . $this->getConnection()->escapeFieldName($index->getName());
                break;
            case IndexInterface::FULLTEXT:
                $result = 'FULLTEXT ' . $this->getConnection()->escapeFieldName($index->getName());
                break;
            default:
                $result = 'INDEX ' . $this->getConnection()->escapeFieldName($index->getName());
                break;
        }

        return $result . ' (' . implode(', ', array_map(function ($field_name) {
            return $this->getConnection()->escapeFieldName($field_name);
        }, $index->getFields())) . ')';
    }

    /**
     * Return name of the connection that will be created for has and belongs to many association.
     *
     * @param  TypeInterface $source
     * @param  TypeInterface $target
     * @return string
     */
    private function getConnectionTableName(TypeInterface $source, TypeInterface $target)
    {
        return $source->getName() . '_' . $target->getName();
    }

    /**
     * Prepare create connection table statement.
     *
     * @param  TypeInterface                  $source
     * @param  TypeInterface                  $target
     * @param  HasAndBelongsToManyAssociation $association
     * @return string
     */
    public function prepareConnectionCreateTableStatement(TypeInterface $source, TypeInterface $target, HasAndBelongsToManyAssociation $association)
    {
        $result = [];

        $result[] = 'CREATE TABLE IF NOT EXISTS ' . $this->getConnection()->escapeTableName($association->getConnectionTableName()) . ' (';

        $left_field_name = $association->getLeftFieldName();
        $right_field_name = $association->getRightFieldName();

        $left_field = (new IntegerField($left_field_name, 0))->unsigned(true)->size($source->getIdField()->getSize());
        $right_field = (new IntegerField($right_field_name, 0))->unsigned(true)->size($target->getIdField()->getSize());

        $result[] = '    ' . $this->prepareFieldStatement($left_field) . ',';
        $result[] = '    ' . $this->prepareFieldStatement($right_field) . ',';
        $result[] = '    ' . $this->prepareIndexStatement(new Index('PRIMARY', [$left_field->getName(), $right_field->getName()], IndexInterface::PRIMARY)) . ',';
        $result[] = '    ' . $this->prepareIndexStatement(new Index($right_field->getName()));

        $result[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return implode("\n", $result);
    }
}
