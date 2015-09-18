<?php

namespace ActiveCollab\DatabaseStructure\AssociationInterface;

/**
 * @package ActiveCollab\DatabaseStructure\AssociationInterface
 */
trait Implementation
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $target_type_name;

    /**
     * Return association name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return target type
     *
     * @return string
     */
    public function getTargetTypeName()
    {
        return $this->target_type_name;
    }
}