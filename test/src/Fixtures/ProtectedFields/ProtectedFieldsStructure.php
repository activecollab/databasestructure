<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\ProtectedFields;

use ActiveCollab\DatabaseStructure\Structure;

/**
 * @package ActiveCollab\DatabaseStructure\Test\Fixtures\ProtectedFields
 */
class ProtectedFieldsStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('no_protected_fields');
        $this->addType('has_protected_fields')->protectFields('field_1', 'field_2');
        $this->addType('multi_protected_fields')->protectFields('field_1', 'field_2')->protectFields('', '')->protectFields('field_2', 'field_3');
    }
}
