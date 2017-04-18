<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks;

use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Structure;

class WriterHasAndBelongsToManyBooksStructure extends Structure
{
    public function configure()
    {
        $this->addType('writers')->addFields([
            (new NameField('name', ''))->required(),
        ])->addAssociations([
            new HasAndBelongsToManyAssociation('books')
        ]);

        $this->addType('books')->addFields([
            (new NameField('name', ''))->required(),
        ])->addAssociations([
            new HasAndBelongsToManyAssociation('writers')
        ]);
    }
}
