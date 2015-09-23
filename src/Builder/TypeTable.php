<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Field\Scalar\Boolean;
use ActiveCollab\DatabaseStructure\Field\Scalar\Date;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTime;
use ActiveCollab\DatabaseStructure\Field\Scalar\Decimal;
use ActiveCollab\DatabaseStructure\Field\Scalar\Field as ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Float;
use ActiveCollab\DatabaseStructure\Field\Scalar\Integer;
use ActiveCollab\DatabaseStructure\Field\Scalar\String;
use ActiveCollab\DatabaseStructure\Field\Scalar\Text;
use ActiveCollab\DatabaseStructure\Field\Scalar\Time;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Type;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class TypeTable extends Database
{
    /**
     * @param Type $type
     */
    public function build(Type $type)
    {
        if ($this->getConnection()->tableExists($type->getName())) {
            $this->triggerEvent('on_table_exists', [$type->getName()]);
        } else {
            $create_table_statement = $this->prepareCreateTableStatement($type);

            $this->getConnection()->execute($create_table_statement);

            $this->triggerEvent('on_table_created', [$type->getName()]);
        }
    }

    /**
     * Prepare CREATE TABLE statement for the given type
     *
     * @param  Type   $type
     * @return string
     */
    public function prepareCreateTableStatement(Type $type)
    {
        $result = [];

        $result[] = 'CREATE TABLE IF NOT EXISTS ' . $this->getConnection()->escapeTableName($type->getName()) . '(';

        foreach ($type->getAllFields() as $field) {
            if ($field instanceof ScalarField) {
                $result[] = '    ' . $this->prepareFieldStatement($field) . ',';
            }
        }

        foreach ($type->getAllIndexes() as $index) {
            $result[] = '    ' . $this->prepareIndexStatement($index) . ',';
        }

        $last_line = count($result) - 1;
        $result[$last_line] = rtrim($result[$last_line], ',');

        $result[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return implode("\n", $result);
    }

    /**
     * Prepare field statement based on the field settings
     *
     * @param  ScalarField $field
     * @return string
     */
    private function prepareFieldStatement(ScalarField $field)
    {
        $result = $this->getConnection()->escapeFieldName($field->getName()) . ' ' . $this->prepareTypeDefinition($field);

        if ($field->getDefaultValue() !== null) {
            $result .= ' NOT NULL';
        }

        if (!($field instanceof Integer && $field->getName() == 'id')) {
            $result .= ' DEFAULT ' . $this->prepareDefaultValue($field);
        }

        return $result;
    }

    /**
     * Prepare type definition for the given field
     *
     * @param  ScalarField $field
     * @return string
     */
    private function prepareTypeDefinition(ScalarField $field)
    {
        if ($field instanceof Integer) {
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

            if ($field->getUnsigned()) {
                $result .= ' UNSIGNED';
            }

            if ($field->getName() == 'id') {
                $result .= ' AUTO_INCREMENT';
            }

            return $result;
        } elseif ($field instanceof Boolean) {
            return 'TINYINT(1) UNSIGNED';
        } elseif ($field instanceof Date) {
            return 'DATE';
        } elseif ($field instanceof DateTime) {
            return 'DATETIME';
        } elseif ($field instanceof Decimal) {
            $result = 'DECIMAL(' . $field->getLength() . ', ' . $field->getScale() . ')';

            if ($field->getUnsigned()) {
                $result .= ' UNSIGNED';
            }

            return $result;
        } elseif ($field instanceof Float) {
            $result = 'FLOAT(' . $field->getLength() . ', ' . $field->getScale() . ')';

            if ($field->getUnsigned()) {
                $result .= ' UNSIGNED';
            }

            return $result;
        } elseif ($field instanceof String) {
            return 'VARCHAR(' . $field->getLength() . ')';
        } elseif ($field instanceof Text) {
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
        } elseif ($field instanceof Time) {
            return 'TIME';
        } else {
            throw new InvalidArgumentException('Field ' . get_class($field) . ' is not a support scalar field');
        }
    }

    /**
     * Prepare default value
     *
     * @param  ScalarField $field
     * @return string
     */
    public function prepareDefaultValue(ScalarField $field)
    {
        $default_value = $field->getDefaultValue();

        if ($default_value === null) {
            return 'NULL';
        }

        if ($field instanceof Date || $field instanceof DateTime) {
            $timestamp = is_int($default_value) ? $default_value : strtotime($default_value);

            if ($field instanceof DateTime) {
                return $this->getConnection()->escapeValue(date('Y-m-d H:i:s', $timestamp));
            } else {
                return $this->getConnection()->escapeValue(date('Y-m-d', $timestamp));
            }
        }

        return $this->getConnection()->escapeValue($default_value);
    }

    /**
     * Prepare index statement
     *
     * @param  Index  $index
     * @return string
     */
    public function prepareIndexStatement(Index $index)
    {
        switch ($index->getIndexType()) {
            case Index::PRIMARY:
                $result = 'PRIMARY KEY';
                break;
            case Index::UNIQUE:
                $result = 'UNIQUE ' . $this->getConnection()->escapeFieldName($index->getName());
                break;
            case Index::FULLTEXT:
                $result = 'FULLTEXT ' . $this->getConnection()->escapeFieldName($index->getName());
                break;
            default:
                $result = 'INDEX ' . $this->getConnection()->escapeFieldName($index->getName());
                break;
        }

        return $result . ' (' . implode(', ', array_map(function($field_name) {
            return $this->getConnection()->escapeFieldName($field_name);
        }, $index->getFields())) . ')';
    }
}