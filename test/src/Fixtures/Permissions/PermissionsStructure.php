<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Permissions;

use ActiveCollab\DatabaseStructure\Structure;

class PermissionsStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('elements')->permissions();
        $this->addType('restrictive_elements')->permissions(true, false);
        $this->addType('reverted_elements')->permissions(true)->permissions(false);
        $this->addType('changed_elements')->permissions(true)->permissions(true, false);
    }
}
