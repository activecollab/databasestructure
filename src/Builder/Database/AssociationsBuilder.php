<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Database;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Association\HasOneAssociation;
use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilderInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;

class AssociationsBuilder extends DatabaseBuilder implements FileSystemBuilderInterface
{
    use StructureSql;

    private $build_path;
    private $appended_constraints = [];
    private $added_connection_tables = [];

    public function getBuildPath(): ?string
    {
        return $this->build_path;
    }

    public function setBuildPath(string $value)
    {
        $this->build_path = $value;

        return $this;
    }

    public function postBuild(): void
    {
        if ($this->getConnection()) {
            foreach ($this->getStructure()->getTypes() as $type) {
                foreach ($type->getAssociations() as $association) {
                    if ($association instanceof BelongsToAssociation) {
                        $this->postBuildBelongsToAssociation($type, $association);
                    } elseif ($association instanceof HasOneAssociation) {
                        $this->postBuildHasOneAssociation($type, $association);
                    } elseif ($association instanceof HasAndBelongsToManyAssociation) {
                        $this->postBuildHasAndBelongsToManyAssociation($type, $association);
                    }
                }
            }
        }
    }

    private function postBuildBelongsToAssociation(TypeInterface $type, BelongsToAssociation $association): void
    {
        $create_constraint_statement = $this->prepareBelongsToConstraintStatement($type, $association);

        $this->appendToStructureSql(
            $create_constraint_statement,
            'Create ' . $this->getConnection()->escapeTableName($association->getConstraintName()) . ' constraint'
        );

        $this->recordAppendedConstraints($association->getConstraintName());

        if ($this->constraintExists($association->getConstraintName(), $association->getTargetTypeName())) {
            $this->triggerEvent(
                'on_association_exists', [
                    $type->getName() . ' belongs to ' . Inflector::singularize($association->getTargetTypeName()),
                ]
            );
        } else {
            $this->getConnection()->execute($create_constraint_statement);
            $this->triggerEvent(
                'on_association_created',
                [
                    $type->getName() . ' belongs to ' . Inflector::singularize($association->getTargetTypeName()),
                ]
            );
        }
    }

    private function postBuildHasOneAssociation(TypeInterface $type, HasOneAssociation $association): void
    {
        $create_constraint_statement = $this->prepareHasOneConstraintStatement($type, $association);

        $this->appendToStructureSql(
            $create_constraint_statement,
            'Create ' . $this->getConnection()->escapeTableName($association->getConstraintName()) . ' constraint'
        );

        $this->recordAppendedConstraints($association->getConstraintName());

        if ($this->constraintExists($association->getConstraintName(), $association->getTargetTypeName())) {
            $this->triggerEvent(
                'on_association_exists',
                [
                    $type->getName() . ' has one ' . Inflector::singularize($association->getTargetTypeName()),
                ]
            );
        } else {
            $this->getConnection()->execute($create_constraint_statement);
            $this->triggerEvent(
                'on_association_created',
                [
                    $type->getName() . ' has one ' . Inflector::singularize($association->getTargetTypeName()),
                ]
            );
        }
    }

    private function postBuildHasAndBelongsToManyAssociation(
        TypeInterface $type,
        HasAndBelongsToManyAssociation $association
    ): void
    {
        $connection_table = $association->getConnectionTableName();

        if (!$this->isAppendedConstraint($association->getLeftConstraintName())) {
            $left_field_name = $association->getLeftFieldName();

            $create_left_field_constraint_statement = $this->prepareHasAndBelongsToManyConstraintStatement(
                $type->getName(),
                $connection_table,
                $association->getLeftConstraintName(),
                $left_field_name
            );

            $this->appendToStructureSql(
                $create_left_field_constraint_statement,
                'Create ' . $this->getConnection()->escapeTableName($association->getLeftConstraintName()) . ' constraint'
            );

            $this->recordAppendedConstraints($association->getLeftConstraintName());

            if ($this->constraintExists($association->getLeftConstraintName(), $association->getSourceTypeName())) {
                $this->triggerEvent(
                    'on_association_skipped',
                    [
                        Inflector::singularize($association->getSourceTypeName()) . ' has many ' . $association->getTargetTypeName(),
                    ]
                );
            } else {
                $this->getConnection()->execute($create_left_field_constraint_statement);
                $this->triggerEvent(
                    'on_association_created',
                    [
                        Inflector::singularize($association->getSourceTypeName()) . ' has many ' . $association->getTargetTypeName(),
                    ]
                );
            }
        }

        if (!$this->isAppendedConstraint($association->getRightConstraintName())) {
            $right_field_name = $association->getRightFieldName();

            $create_right_field_constraint_statement = $this->prepareHasAndBelongsToManyConstraintStatement(
                $association->getTargetTypeName(),
                $connection_table,
                $association->getRightConstraintName(),
                $right_field_name
            );

            $this->appendToStructureSql(
                $create_right_field_constraint_statement,
                'Create ' . $this->getConnection()->escapeTableName($association->getRightConstraintName()) . ' constraint'
            );

            $this->recordAppendedConstraints($association->getRightConstraintName());

            if ($this->constraintExists($association->getRightConstraintName(), $association->getTargetTypeName())) {
                $this->triggerEvent(
                    'on_association_skipped',
                    [
                        Inflector::singularize($association->getTargetTypeName()) . ' has many ' . $association->getSourceTypeName(),
                    ]
                );
            } else {
                $this->getConnection()->execute($create_right_field_constraint_statement);
                $this->triggerEvent(
                    'on_association_created',
                    [
                        Inflector::singularize($association->getTargetTypeName()) . ' has many ' . $association->getSourceTypeName(),
                    ]
                );
            }
        }
    }

    public function prepareBelongsToConstraintStatement(TypeInterface $type, BelongsToAssociation $association): string
    {
        $result = [];

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($type->getName());
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($association->getConstraintName());
        $result[] = '    FOREIGN KEY (' . $this->getConnection()->escapeFieldName($association->getFieldName()) . ') REFERENCES ' . $this->getConnection()->escapeTableName($association->getTargetTypeName()) . '(`id`)';

        if ($association->isRequired()) {
            $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE;';
        } else {
            $result[] = '    ON UPDATE SET NULL ON DELETE SET NULL;';
        }

        return implode("\n", $result);
    }

    public function prepareHasOneConstraintStatement(TypeInterface $type, HasOneAssociation $association): string
    {
        $result = [];

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($type->getName());
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($association->getConstraintName());
        $result[] = '    FOREIGN KEY (' . $this->getConnection()->escapeFieldName($association->getFieldName()) . ') REFERENCES ' . $this->getConnection()->escapeTableName($association->getTargetTypeName()) . '(`id`)';

        if ($association->isRequired()) {
            $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE;';
        } else {
            $result[] = '    ON UPDATE SET NULL ON DELETE SET NULL;';
        }

        return implode("\n", $result);
    }

    public function prepareHasAndBelongsToManyConstraintStatement(
        string $type_name,
        string $connection_table,
        string $constraint_name,
        string $field_name
    ): string
    {
        $result = [];

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($connection_table);
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($constraint_name);
        $result[] = '    FOREIGN KEY (' . $field_name . ') REFERENCES ' . $this->getConnection()->escapeTableName($type_name) . '(`id`)';
        $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE;';

        return implode("\n", $result);
    }

    /**
     * Check if constraint exists at referenced table.
     *
     * @param  string $constraint_name
     * @param  string $referencing_table
     * @return bool
     */
    private function constraintExists($constraint_name, $referencing_table)
    {
        return (bool) $this->getConnection()->executeFirstCell(
            'SELECT COUNT(*) AS "row_count" FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE CONSTRAINT_NAME = ? AND REFERENCED_TABLE_NAME = ?;',
            $constraint_name,
            $referencing_table
        );
    }

    private function isAppendedConstraint(string $constraint_name): bool
    {
        return in_array($constraint_name, $this->appended_constraints);
    }

    private function recordAppendedConstraints(string $constraint_name): void
    {
        $this->appended_constraints[] = $constraint_name;
    }
}
