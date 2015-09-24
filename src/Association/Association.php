<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
abstract class Association implements AssociationInterface
{
    /**
     * @var string
     */
    private $source_type_name;

    /**
     * Return source type name
     *
     * @return string
     */
    public function getSourceTypeName()
    {
        return $this->source_type_name;
    }

    /**
     * Set source type name
     *
     * @param  string $source_type_name
     * @return $this
     */
    public function &setSourceTypeName($source_type_name)
    {
        $this->source_type_name = $source_type_name;

        return $this;
    }
}