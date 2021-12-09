<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Blog;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\AdditionalPropertiesField;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTimeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\EnumField;
use ActiveCollab\DatabaseStructure\Field\Scalar\TextField;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * Blog structure is used for testing how builers are doing their job.
 */
class BlogStructure extends Structure
{
    /**
     * Configure the structure.
     */
    public function configure()
    {
        $this->addType('categories')->expectedDatasetSize(FieldInterface::SIZE_SMALL)->addFields([
            (new NameField())->unique(),
        ])->addAssociations([
            new HasAndBelongsToManyAssociation('posts'),
        ]);

        $this->addType('posts')->addFields([
            new NameField(),
            new TextField('body'),
            new DateTimeField('created_at'),
            new DateTimeField('published_at'),
            new AdditionalPropertiesField(),
            (new EnumField('is_featured', 'no'))->possibilities('yes', 'no'),
        ])->addIndexes([
            new Index('published_at'),
        ])->addAssociations([
            new HasManyAssociation('comments'),
        ]);

        $this->addType('comments')->addFields([
            new TextField('body'),
            new DateTimeField('created_at'),
        ])->addIndexes([
            new Index('created_at'),
        ])->addAssociations([
            new BelongsToAssociation('post'),
        ]);
    }
}
