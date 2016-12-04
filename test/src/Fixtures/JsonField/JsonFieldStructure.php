<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\JsonField;

use ActiveCollab\DatabaseStructure\Field\Scalar\DateField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\BoolValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\IntValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractor;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\JsonSerialization
 */
class JsonFieldStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('stats_snapshots')->addFields([
            new DateField('day'),
            (new JsonField('stats'))
                ->extractValue('plan_name', '$.plan_name', null, ValueExtractor::class, true, true)
                ->extractValue('number_of_active_users', '$.users.num_active', null, IntValueExtractor::class, true)
                ->extractValue('is_used_on_day', '$.is_used_on_day', null, BoolValueExtractor::class, false),
        ])->addIndex(new Index('day'));
    }
}
