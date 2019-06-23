<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Timestamps;

use ActiveCollab\DatabaseStructure\Field\Composite\CreatedAtField;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Field\Composite\UpdatedAtField;
use ActiveCollab\DatabaseStructure\Structure;

class TimestampsStructure extends Structure
{
    public function configure()
    {
        $this->addType('timestamped_entries')->addFields([
            new NameField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
