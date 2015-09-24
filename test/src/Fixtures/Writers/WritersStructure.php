<?php
namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Writers;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
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
            new NameField(),
            (new DateField('birthday'))->required(),
        ])->addIndexes([
            new Index('birthday'),
        ])->addAssociations([
            new HasManyAssociation('books'),
        ]);

        $this->addType('books')->addFields([
            (new NameField('title', '', true))->required()->unique('writer_id'),
        ])->addAssociations([
            new BelongsToAssociation('author', 'writers'),
            new HasManyAssociation('chapters'),
        ]);

        $this->addType('chapters')->addFields([
            (new NameField('title', '', true))->required()->unique('book_id'),
            new PositionField(),
        ])->addAssociations([
            new BelongsToAssociation('book'),
        ]);
    }
}