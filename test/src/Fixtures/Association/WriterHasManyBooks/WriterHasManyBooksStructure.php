<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBooks;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Structure;

class WriterHasManyBooksStructure extends Structure
{
    public function configure()
    {
        $this->addType('writers')->addFields([
            (new NameField('name', ''))->required(),
        ])->addAssociations([
            (new HasManyAssociation('books'))
                ->required(false),
        ]);

        $this->addType('books')->addFields([
            (new NameField('name', ''))->required(),
        ])->addAssociations([
            (new BelongsToAssociation('writer'))->required(false),
        ]);
    }
}
