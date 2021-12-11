<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\AddPermissions;

use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Structure;

class AddPermissionsStructure extends Structure
{
    /**
     * @var string|null
     */
    private $add_permissions;

    /**
     * @param string|null $add_permissions
     */
    public function __construct($add_permissions = null)
    {
        $this->add_permissions = $add_permissions;

        parent::__construct();
    }

    
    public function configure()
    {
        if ($this->add_permissions) {
            $this->setConfig('add_permissions', $this->add_permissions);
        }

        $this->addType('elements')->addFields([new NameField()]);
    }
}
