<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTimeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DecimalField;
use ActiveCollab\DatabaseStructure\Field\Scalar\EnumField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Field as ScalarField;
use ActiveCollab\DatabaseStructure\Field\Scalar\FloatField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\TextField;
use ActiveCollab\DatabaseStructure\Field\Scalar\TimeField;
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
     * Build path. If empty, class will be built to memory
     *
     * @var string
     */
    private $build_path;

    /**
     * Return build path
     *
     * @return string
     */
    public function getBuildPath()
    {
        return $this->build_path;
    }

    /**
     * Set build path. If empty, class will be built in memory
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
     * Execute prior to type build
     */
    public function preBuild()
    {
        if ($structure_sql_path = $this->getStructureSqlPath()) {
            file_put_contents($structure_sql_path, '');
            $this->triggerEvent('on_structure_sql_built', [$structure_sql_path]);
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
     * Prepare CREATE TABLE statement for the given type
     *
     * @param  TypeInterface $type
     * @return string
     */
    public function prepareCreateTableStatement(TypeInterface $type)
    {
        $result = [];

        $result[] = 'CREATE TABLE IF NOT EXISTS ' . $this->getConnection()->escapeTableName($type->getName()) . ' (';

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

        if (!($field instanceof IntegerField && $field->getName() == 'id')) {
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
            return 'ENUM(' . implode(',', array_map(function($possibility) {
                return $this->getConnection()->escapeValue($possibility);
            }, $field->getPossibilities())) . ')';
        } elseif ($field instanceof FloatField) {
            $result = 'FLOAT(' . $field->getLength() . ', ' . $field->getScale() . ')';

            if ($field->isUnsigned()) {
                $result .= ' UNSIGNED';
            }

            return $result;
        } elseif ($field instanceof StringField) {
            return 'VARCHAR(' . $field->getLength() . ')';
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
     * Prepare index statement
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

        return $result . ' (' . implode(', ', array_map(function($field_name) {
            return $this->getConnection()->escapeFieldName($field_name);
        }, $index->getFields())) . ')';
    }

    /**
     * Return name of the connection that will be created for has and belongs to many association
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
     * Prepare create connection table statement
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

        $left_field = (new IntegerField($left_field_name))->unsigned(true)->size($source->getIdField()->getSize());
        $right_field = (new IntegerField($right_field_name))->unsigned(true)->size($target->getIdField()->getSize());

        $result[] = '    ' . $this->prepareFieldStatement($left_field) . ',';
        $result[] = '    ' . $this->prepareFieldStatement($right_field) . ',';
        $result[] = '    ' . $this->prepareIndexStatement(new Index('PRIMARY', [$left_field->getName(), $right_field->getName()], IndexInterface::PRIMARY)) . ',';
        $result[] = '    ' . $this->prepareIndexStatement(new Index($right_field->getName()));

        $result[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return implode("\n", $result);
    }
}
