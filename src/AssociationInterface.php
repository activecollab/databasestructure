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
     * Return source type name
     *
     * @return string
     */
    public function getSourceTypeName();

    /**
     * Set source type name
     *
     * @param  string $source_type_name
     * @return $this
     */
    public function &setSourceTypeName($source_type_name);

    /**
     * Build class methods
     *
     * @param string        $namespace
     * @param TypeInterface $source_type
     * @param TypeInterface $target_type
     * @param array         $result
     */
    public function buildClassMethods($namespace, TypeInterface $source_type, TypeInterface $target_type, array &$result);
}
