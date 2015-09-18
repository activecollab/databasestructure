<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasMany implements AssociationInterface
{
    use AssociationInterface\Implementation;

    /**
     * Return a list of fields that are to be added to the source type
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [];
    }
}