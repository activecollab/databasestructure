<?php
namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Writers;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
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
            new HasManyAssociation('books'),
        ]);

        $this->addType('books')->addFields([
            (new Name('title', '', true))->required()->unique('writer_id'),
        ])->addAssociations([
            new BelongsToAssociation('author', 'writers'),
            new HasManyAssociation('chapters'),
        ]);

        $this->addType('chapters')->addFields([
            (new Name('title', '', true))->required()->unique('book_id'),
            new Position(),
        ])->addAssociations([
            new BelongsToAssociation('book'),
        ]);
    }
}