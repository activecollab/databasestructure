<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface AssociationInterface
{
    /**
     * Get association name, in underscore notation.
     *
     * @return string
     */
    public function getName();

    /**
     * Return name of the target type.
     *
     * @return string
     */
    public function getTargetTypeName();

    /**
     * Return source type name.
     *
     * @return string
     */
    public function getSourceTypeName();

    /**
     * Set source type name.
     *
     * @param  string $source_type_name
     * @return $this
     */
    public function &setSourceTypeName($source_type_name);

    public function getAttributes(): array;

    public function buildAssociatedEntitiesManagerConstructionLine(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    );

    public function buildAttributeInterception(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    );

    public function buildClassPropertiesAndMethods(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        array &$result
    );
}
