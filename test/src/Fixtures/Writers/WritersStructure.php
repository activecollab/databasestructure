<?php
namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Writers;

use ActiveCollab\DatabaseStructure\Association\BelongsTo;
use ActiveCollab\DatabaseStructure\Association\HasMany;
use ActiveCollab\DatabaseStructure\Field\Composite\Name;
use ActiveCollab\DatabaseStructure\Field\Composite\Position;
use ActiveCollab\DatabaseStructure\Field\Scalar\Date;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\Writers
 */
class WritersStructure extends Structure
{
    /**
     * Configure the structure
     */
    public function configure()
    {
        $this->addType('writers')->addFields([
            new Name(),
            (new Date('birthday'))->required(),
        ])->addIndexes([
            new Index('birthday'),
        ])->addAssociations([
            new HasMany('books'),
        ]);

        $this->addType('books')->addFields([
            (new Name('title', '', true))->required()->unique('writer_id'),
        ])->addAssociations([
            new BelongsTo('author', 'writers'),
            new HasMany('chapters'),
        ]);

        $this->addType('chapters')->addFields([
            (new Name('title', '', true))->required()->unique('book_id'),
            new Position(),
        ])->addAssociations([
            new BelongsTo('book'),
        ]);
    }
}