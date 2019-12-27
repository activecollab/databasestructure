<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\PositionContext;

use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Structure;

class PositionContextStructure extends Structure
{
    public function configure()
    {
        $this->addType('position_context_tail_entries')->addFields([
            (new IntegerField('application_id', 0))->unsigned(),
            (new IntegerField('shard_id', 0))->unsigned(),
            (new PositionField())->context('application_id', 'shard_id')->tail(),
        ]);

        $this->addType('position_context_head_entries')->addFields([
            (new IntegerField('application_id', 0))->unsigned(),
            (new IntegerField('shard_id', 0))->unsigned(),
            (new PositionField())->context('application_id', 'shard_id')->head(),
        ]);
    }
}
