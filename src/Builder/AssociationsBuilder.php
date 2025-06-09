<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Association\HasOneAssociation;
use ActiveCollab\DatabaseStructure\Builder\SqlElement\ForeignKey;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

class AssociationsBuilder extends DatabaseBuilder implements FileSystemBuilderInterface
{
    use StructureSql;

    /**
     * Build path. If empty, class will be built to memory.
     *
     * @var string
     */
    private $build_path;

    public function getBuildPath(): ?string
    {
        return $this->build_path;
    }

    public function setBuildPath(?string $value): FileSystemBuilderInterface
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

    private function postBuildBelongsToAssociation(TypeInterface $type, BelongsToAssociation $association)
    {
        $inflector = $this->getInflector();

        $create_constraint_statement = $this->prepareBelongsToConstraintStatement($type, $association);
        $this->appendToStructureSql(
            new ForeignKey($type->getTableName(), $association->getConstraintName()),
            $create_constraint_statement,
            sprintf('Create %s constraint', $this->getConnection()->escapeTableName($association->getConstraintName())),
        );

        if ($this->constraintExists($association->getConstraintName(), $association->getTargetTypeName())) {
            $this->triggerEvent(
                'on_association_exists',
                [
                    $type->getName() . ' belongs to ' . $inflector->singularize($association->getTargetTypeName()),
                ],
            );
        } else {
            $this->getConnection()->execute($create_constraint_statement);
            $this->triggerEvent(
                'on_association_created',
                [
                    $type->getName() . ' belongs to ' . $inflector->singularize($association->getTargetTypeName()),
                ],
            );
        }
    }

    private function postBuildHasOneAssociation(TypeInterface $type, HasOneAssociation $association)
    {
        $inflector = $this->getInflector();

        $create_constraint_statement = $this->prepareHasOneConstraintStatement($type, $association);
        $this->appendToStructureSql(
            new ForeignKey($type->getTableName(), $association->getConstraintName()),
            $create_constraint_statement,
            sprintf('Create %s constraint', $this->getConnection()->escapeTableName($association->getConstraintName())),
        );

        if ($this->constraintExists($association->getConstraintName(), $association->getTargetTypeName())) {
            $this->triggerEvent('on_association_exists', [$type->getName() . ' has one ' . $inflector->singularize($association->getTargetTypeName())]);
        } else {
            $this->getConnection()->execute($create_constraint_statement);
            $this->triggerEvent('on_association_created', [$type->getName() . ' has one ' . $inflector->singularize($association->getTargetTypeName())]);
        }
    }

    private function postBuildHasAndBelongsToManyAssociation(TypeInterface $type, HasAndBelongsToManyAssociation $association)
    {
        $connection_table = $association->getConnectionTableName();

        $left_field_name = $association->getLeftFieldName();
        $create_left_field_constraint_statement = $this->prepareHasAndBelongsToManyConstraintStatement(
            $type->getName(),
            $connection_table,
            $association->getLeftConstraintName(),
            $left_field_name,
        );

        $this->appendToStructureSql(
            new ForeignKey($connection_table, $association->getLeftConstraintName()),
            $create_left_field_constraint_statement,
            sprintf('Create %s constraint', $this->getConnection()->escapeTableName($association->getLeftConstraintName())),
        );

        $right_field_name = $association->getRightFieldName();
        $create_right_field_constraint_statement = $this->prepareHasAndBelongsToManyConstraintStatement(
            $association->getTargetTypeName(),
            $connection_table,
            $association->getRightConstraintName(),
            $right_field_name,
        );

        $this->appendToStructureSql(
            new ForeignKey($connection_table, $association->getRightConstraintName()),
            $create_right_field_constraint_statement,
            sprintf('Create %s constraint', $this->getConnection()->escapeTableName($association->getRightConstraintName())),
        );

        $inflector = $this->getInflector();

        if ($this->constraintExists($association->getLeftConstraintName(), $association->getSourceTypeName())) {
            $this->triggerEvent('on_association_skipped', [$inflector->singularize($association->getSourceTypeName()) . ' has many ' . $association->getTargetTypeName()]);
        } else {
            $this->getConnection()->execute($create_left_field_constraint_statement);
            $this->triggerEvent('on_association_created', [$inflector->singularize($association->getSourceTypeName()) . ' has many ' . $association->getTargetTypeName()]);
        }

        if ($this->constraintExists($association->getRightConstraintName(), $association->getTargetTypeName())) {
            $this->triggerEvent('on_association_skipped', [$inflector->singularize($association->getTargetTypeName()) . ' has many ' . $association->getSourceTypeName()]);
        } else {
            $this->getConnection()->execute($create_right_field_constraint_statement);
            $this->triggerEvent('on_association_created', [$inflector->singularize($association->getTargetTypeName()) . ' has many ' . $association->getSourceTypeName()]);
        }
    }

    /**
     * Prepare belongs to constraint statement.
     *
     * @param  TypeInterface        $type
     * @param  BelongsToAssociation $association
     * @return string
     */
    public function prepareBelongsToConstraintStatement(TypeInterface $type, BelongsToAssociation $association)
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

    /**
     * Prepare has one constraint statement.
     *
     * @param  TypeInterface     $type
     * @param  HasOneAssociation $association
     * @return string
     */
    public function prepareHasOneConstraintStatement(TypeInterface $type, HasOneAssociation $association)
    {
        $result = [];

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($type->getTableName());
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($association->getConstraintName());
        $result[] = '    FOREIGN KEY (' . $this->getConnection()->escapeFieldName($association->getFieldName()) . ') REFERENCES ' . $this->getConnection()->escapeTableName($association->getTargetTypeName()) . '(`id`)';

        if ($association->isRequired()) {
            $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE;';
        } else {
            $result[] = '    ON UPDATE SET NULL ON DELETE SET NULL;';
        }

        return implode("\n", $result);
    }

    /**
     * Prepare has and belongs to many constraint statement.
     *
     * @param  string $type_name
     * @param  string $connection_table
     * @param  string $constraint_name
     * @param  string $field_name
     * @return string
     */
    public function prepareHasAndBelongsToManyConstraintStatement($type_name, $connection_table, $constraint_name, $field_name)
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
        return (bool) $this->getConnection()->executeFirstCell('SELECT COUNT(*) AS "row_count" FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE CONSTRAINT_NAME = ? AND REFERENCED_TABLE_NAME = ?;', $constraint_name, $referencing_table);
    }

    private ?\Doctrine\Inflector\Inflector $inflector = null;

    private function getInflector(): Inflector
    {
        if ($this->inflector === null) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
