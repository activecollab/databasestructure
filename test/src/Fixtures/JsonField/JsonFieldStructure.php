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
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\DateTimeValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\DateValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\FloatValueExtractor;
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
                ->extractValue('is_used_on_day', '$.is_used_on_day', null, BoolValueExtractor::class, false)
                ->addValueExtractor(new FloatValueExtractor('execution_time', '$.exec_time', 0))
                ->addValueExtractor(new DateValueExtractor('important_date_1', '$.important_date_1', '2013-10-02'))
                ->addValueExtractor(new DateTimeValueExtractor('important_date_2_with_time', '$.important_date_2_with_time', '2016-05-09 09:11:00')),
        ])->addIndex(new Index('day'));
    }
}
