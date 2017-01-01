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

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\JsonSerialization
 */
class RequiredJsonSerializationStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('required_key_values')->addFields([
            (new NameField())->unique(),
            (new JsonField('value', '{}'))->required(),
        ]);
    }
}
