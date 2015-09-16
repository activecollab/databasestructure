<?php
namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Writers;

use ActiveCollab\DatabaseStructure\Structure;

class WritersStructure extends Structure
{
    public function configure()
    {
        $this->addType('writers')->addFields([

        ]);

        $this->addType('books')->addFields([

        ]);
    }
}