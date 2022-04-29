<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\PositionTail;

use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Structure;

class PositionTailStructure extends Structure
{
    public function configure()
    {
        $this->addType('position_tail_entries')->addFields(
            (new PositionField())->tail(),
        );
    }
}
