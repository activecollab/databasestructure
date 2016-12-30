<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;

interface ProgramToInterfaceInterface
{
    public function getAccepts(): ? string;

    public function &accepts(string $interface_name = null) : AssociationInterface;
}
