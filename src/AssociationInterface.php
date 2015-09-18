<?php

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface AssociationInterface
{
    /**
     * Get association name, in underscore notation
     *
     * @return string
     */
    public function getName();

    /**
     * Return name of the target type
     *
     * @return string
     */
    public function getTargetTypeName();

    /**
     * Return a list of fields that are to be added to the source type
     *
     * @return FieldInterface[]
     */
    public function getFields();

//    /**
//     * Return a connection table definition
//     *
//     * @return null
//     */
//    public function getConnectionTable();
}