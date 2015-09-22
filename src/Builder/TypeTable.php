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
        $field_statements = [];

        foreach ($type->getAllFields() as $field) {
            if ($field instanceof ScalarField) {
                $field_statements[] = $this->prepareFieldStatement($field);
            }
        }

        var_dump($field_statements);
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

        $result .= ' DEFAULT ' . $this->prepareDefaultValue($field);

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
}