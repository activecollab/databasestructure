<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Blog;

use ActiveCollab\DatabaseStructure\Association\BelongsTo;
use ActiveCollab\DatabaseStructure\Association\HasMany;
use ActiveCollab\DatabaseStructure\Field\Composite\Name;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTime;
use ActiveCollab\DatabaseStructure\Field\Scalar\Text;
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
        $this->addType('categories')->addFields([
            (new Name())->unique(),
        ])->addAssociations([
            new HasMany('posts'),
        ]);

        $this->addType('posts')->addFields([
            new Name(),
            new Text('body'),
            new DateTime('created_at'),
            new DateTime('published_at'),
        ])->addAssociations([
            new BelongsTo('category'),
            new HasMany('comments'),
        ]);

        $this->addType('comments')->addFields([
            new Text('body'),
        ])->addAssociations([
            new BelongsTo('post'),
        ]);
    }
}