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

    /**
     * Return a list of indexes
     *
     * @return IndexInterface[]
     */
    public function getIndexes();

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
     * @param string $namespace
     * @param Type   $source_type
     * @param Type   $target_type
     * @param array  $result
     */
    public function buildClassMethods($namespace, Type $source_type, Type $target_type, array &$result);
}