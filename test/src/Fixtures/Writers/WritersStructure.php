<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Writers;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\AdditionalPropertiesField;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\Writers
 */
class WritersStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('writers')->addFields([
            new NameField('name', ''),
            (new DateField('birthday'))->required(),
            new BooleanField('is_awesome', true),
        ])->addIndexes([
            new Index('birthday'),
        ])->addAssociations([
            new HasManyAssociation('books'),
        ])->orderBy('name')->serialize('name', 'birthday');

        $this->addType('books')->addFields([
            (new NameField('title', '', true))->required()->unique('writer_id'),
        ])->addAssociations([
            new BelongsToAssociation('author', 'writers'),
            new HasManyAssociation('chapters'),
        ]);

        $this->addType('chapters')->addFields([
            (new NameField('title', '', true))->required()->unique('book_id'),
            new AdditionalPropertiesField(),
            new PositionField(),
        ])->addAssociations([
            new BelongsToAssociation('book'),
        ])->orderBy('position');
    }
}
