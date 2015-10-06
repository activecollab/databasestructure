<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\PositionTail;

use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\PositionTail
 */
class PositionTailStructure extends Structure
{
    /**
     * Configure the structure
     */
    public function configure()
    {
        $this->addType('position_tail_entries')->addFields([
            (new PositionField())->tail(),
        ]);
    }
}