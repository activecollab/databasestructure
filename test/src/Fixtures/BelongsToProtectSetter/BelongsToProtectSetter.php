<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\BelongsToProtectSetter;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\BelongsToProtectSetter
 */
class BelongsToProtectSetter extends Structure
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addType('users')->addAssociations([
            new HasManyAssociation('books'),
            new HasManyAssociation('notes'),
        ]);

        $this->addType('books')->addAssociations([
            new BelongsToAssociation('user'),
        ]);

        $this->addType('notes')->addAssociations([
            (new BelongsToAssociation('user'))->protectSetter(),
        ]);
    }
}
