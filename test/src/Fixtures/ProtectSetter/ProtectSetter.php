<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\ProtectSetter;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Structure;

class ProtectSetter extends Structure
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addType('users')->addFields([
            new StringField('unprotected_setter'),
            (new StringField('protected_setter'))->protectSetter(),
        ])->addAssociations([
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
