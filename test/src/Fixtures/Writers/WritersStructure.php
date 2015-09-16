<?php
namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Writers;

use ActiveCollab\DatabaseStructure\Field\Composite\Name;
use ActiveCollab\DatabaseStructure\Field\Scalar\Date;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Structure;

class WritersStructure extends Structure
{
    public function configure()
    {
        $this->addType('writers')->addFields([
            new Name(),
            (new Date('birthday'))->required(),
        ])->addIndexes([
            new Index('birthday'),
        ])->hasMany('books');

        $this->addType('books')->addFields([
            (new Name('title', '', true))->required()->unique('writer_id'),
        ])->belongsTo('writers')->hasMany('chapters');

        $this->addType('chapters')->addFields([
            (new Name('title', '', true))->required()->unique('book_id'),
            new Position(),
        ])->belongsTo('books');
    }
}