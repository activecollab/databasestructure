<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\JsonSerialization;

use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Structure;

class JsonSerializationStructure extends Structure
{
    
    public function configure()
    {
        $this->addType('key_values')->addFields(
            (new NameField())->unique(),
            new JsonField('value'),
        );
    }
}
