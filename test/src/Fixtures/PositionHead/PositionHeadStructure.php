<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\PositionHead;

use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Structure;

class PositionHeadStructure extends Structure
{
    public function configure()
    {
        $this->addType('position_head_entries')->addFields(
            (new PositionField())->head(),
        );
    }
}
