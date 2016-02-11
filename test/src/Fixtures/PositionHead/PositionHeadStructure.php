<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\PositionHead;

use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\PositionHead
 */
class PositionHeadStructure extends Structure
{
    /**
     * Configure the structure.
     */
    public function configure()
    {
        $this->addType('position_head_entries')->addFields([
            (new PositionField())->head(),
        ]);
    }
}
