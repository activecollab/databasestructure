<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Triggers;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Structure;
use ActiveCollab\DatabaseStructure\Trigger\BeforeInsertTrigger;
use ActiveCollab\DatabaseStructure\Trigger\BeforeUpdateTrigger;

class TriggersStructure extends Structure
{
    public function configure()
    {
        $this->addType('triggers')->addFields(
            new IntegerField('num'),
        )->addTriggers(
            new BeforeInsertTrigger('num_plus_two', 'SET NEW.`num` = NEW.`num` + 2;'),
            new BeforeUpdateTrigger('num_plus_three', '
              SET @inc = 3;
              SET NEW.`num` = NEW.`num` + @inc;
            '),
        );
    }
}
