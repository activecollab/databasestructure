<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\PositionContext;

use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\PositionTail
 */
class PositionContextStructure extends Structure
{
    /**
     * Configure the structure
     */
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