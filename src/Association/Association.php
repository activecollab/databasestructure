<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;

abstract class Association implements AssociationInterface
{
    /**
     * @var string
     */
    private $source_type_name;

    /**
     * Return source type name.
     *
     * @return string
     */
    public function getSourceTypeName()
    {
        return $this->source_type_name;
    }

    /**
     * Set source type name.
     *
     * @param  string $source_type_name
     * @return $this
     */
    public function &setSourceTypeName($source_type_name)
    {
        $this->source_type_name = $source_type_name;

        return $this;
    }

    public function buildAttributeInterception(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    )
    {
    }
}
