<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToMany;
use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Association\BelongsTo;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class Associations extends Database
{
    /**
     * Execute after types are built
     */
    public function postBuild()
    {
        if ($this->getConnection()) {
            foreach ($this->getStructure()->getTypes() as $type) {
                foreach ($type->getAssociations() as $association) {
                    if ($association instanceof BelongsTo) {
                        if ($this->constraintExists($association->getConstraintName(), $association->getTargetTypeName())) {
                            $this->triggerEvent('on_association_exists', [$type->getName() . ' belongs to ' . $association->getTargetTypeName()]);
                        } else {
                            $this->getConnection()->execute($this->prepareBelongsToConstraintStatement($type, $association));
                            $this->triggerEvent('on_association_created', [$type->getName() . ' belongs to ' . $association->getTargetTypeName()]);
                        }
                    } elseif ($association instanceof HasAndBelongsToMany) {
                        $connection_table = $association->getConnectionTableName();

                        $left_field_name = $association->getLeftFieldName();
                        $right_field_name = $association->getRightFieldName();

                        $this->getConnection()->execute($this->prepareHasAndBelongsToManyConstraintStatement($type->getName(), $connection_table, $left_field_name));
                        $this->getConnection()->execute($this->prepareHasAndBelongsToManyConstraintStatement($association->getTargetTypeName(), $connection_table, $right_field_name));
                    }
                }
            }
        }
    }

    /**
     * Prepare belongs to constraint statement
     *
     * @param  Type      $type
     * @param  BelongsTo $association
     * @return string
     */
    public function prepareBelongsToConstraintStatement(Type $type, BelongsTo $association)
    {
        $result = [];

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($type->getName());
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($association->getConstraintName());
        $result[] = '    FOREIGN KEY (' . $this->getConnection()->escapeFieldName($association->getFieldName()) . ') REFERENCES ' . $this->getConnection()->escapeTableName($association->getTargetTypeName()) . '(`id`)';

        if ($association->getOptional()) {
            $result[] = '    ON UPDATE SET NULL ON DELETE SET NULL';
        } else {
            $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE';
        }

        return implode("\n", $result);
    }


    /**
     * Prepare has and belongs to many constraint statement
     *
     * @param  string $type_name
     * @param  string $connection_table
     * @param  string $field_name
     * @return string
     */
    public function prepareHasAndBelongsToManyConstraintStatement($type_name, $connection_table, $field_name)
    {
        $result = [];

        $constraint_name = "{$field_name}_constraint";

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($connection_table);
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($constraint_name);
        $result[] = '    FOREIGN KEY (' . $field_name . ') REFERENCES ' . $this->getConnection()->escapeTableName($type_name) . '(`id`)';
        $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE';

        return implode("\n", $result);
    }

    private function constraintExists($constraint_name, $referencing_table)
    {
        return (boolean) $this->getConnection()->executeFirstCell('select
            COUNT(*) AS "row_count"
        from INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        where
            CONSTRAINT_NAME = ? AND REFERENCED_TABLE_NAME = ?;', $constraint_name, $referencing_table);
    }
}