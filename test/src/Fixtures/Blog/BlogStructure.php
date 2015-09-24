<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Blog;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\Name;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTime;
use ActiveCollab\DatabaseStructure\Field\Scalar\Enum;
use ActiveCollab\DatabaseStructure\Field\Scalar\Text;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * Blog structure is used for testing how builers are doing their job
 *
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\Blog
 */
class BlogStructure extends Structure
{
    /**
     * Configure the structure
     */
    public function configure()
    {
        $this->addType('categories')->expectedDatasetSize(FieldInterface::SIZE_SMALL)->addFields([
            (new Name())->unique(),
        ])->addAssociations([
            new HasAndBelongsToManyAssociation('posts'),
        ]);

        $this->addType('posts')->addFields([
            new Name(),
            new Text('body'),
            new DateTime('created_at'),
            new DateTime('published_at'),
            (new Enum('is_featured', 'no'))->possibilities('yes', 'no'),
        ])->addIndexes([
            new Index('published_at'),
        ])->addAssociations([
            new HasManyAssociation('comments'),
        ]);

        $this->addType('comments')->addFields([
            new Text('body'),
            new DateTime('created_at'),
        ])->addIndexes([
            new Index('created_at'),
        ])->addAssociations([
            new BelongsToAssociation('post'),
        ]);
    }
}