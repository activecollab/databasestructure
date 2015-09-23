<?php

namespace ActiveCollab\DatabaseStructure\Builder;

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
                        $this->getConnection()->execute($this->prepareBelongsToConstraintStatement($type, $association));
                        $this->triggerEvent('on_association', [$type->getName() . ' belongs to ' . $association->getTargetTypeName()]);
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

        $constraint_name = $association->getName() . '_' . $type->getName() . '_constraint';

        $result[] = 'ALTER TABLE ' . $this->getConnection()->escapeTableName($type->getName());
        $result[] = '    ADD CONSTRAINT ' . $this->getConnection()->escapeFieldName($constraint_name);
        $result[] = '    FOREIGN KEY (' . $this->getConnection()->escapeFieldName($association->getFieldName()) . ') REFERENCES ' . $this->getConnection()->escapeTableName($association->getTargetTypeName()) . '(`id`)';

        if ($association->getOptional()) {
            $result[] = '    ON UPDATE SET NULL ON DELETE SET NULL';
        } else {
            $result[] = '    ON UPDATE CASCADE ON DELETE CASCADE';
        }

        return implode("\n", $result);
    }
}