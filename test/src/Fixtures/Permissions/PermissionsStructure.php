<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Permissions;

use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\Permissions
 */
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
