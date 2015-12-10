<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Permissions;

use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\Writers
 */
class PermissionsStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('elements')->addFields([
            new NameField(),
        ])->permissions();

        $this->addType('restrictive_elements')->addFields([
            new NameField(),
        ])->permissions(true, false);
    }
}
