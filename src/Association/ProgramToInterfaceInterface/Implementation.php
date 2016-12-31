<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association\ProgramToInterfaceInterface;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use InvalidArgumentException;

trait Implementation
{
    /**
     * @var string|null
     */
    private $accepts;

    public function getAccepts(): ?string
    {
        return $this->accepts;
    }

    /**
     * @param  string|null                $interface_name
     * @return AssociationInterface|$this
     */
    public function &accepts(string $interface_name = null): AssociationInterface
    {
        if ($interface_name && !interface_exists($interface_name)) {
            throw new InvalidArgumentException("Interface '$interface_name' not found.");
        }
        $this->accepts = $interface_name;

        return $this;
    }
}
