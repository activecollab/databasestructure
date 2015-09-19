<?php

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Type;

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

    /**
     * Build class methods
     *
     * @param string $namespace
     * @param Type   $source_type
     * @param Type   $target_type
     * @param array  $result
     */
    public function buildClassMethods($namespace, Type $source_type, Type $target_type, array &$result);
}