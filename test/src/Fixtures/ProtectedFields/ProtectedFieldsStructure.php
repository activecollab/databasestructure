<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\ProtectedFields;

use ActiveCollab\DatabaseStructure\Structure;

class ProtectedFieldsStructure extends Structure
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addType('no_protected_fields');
        $this->addType('has_protected_fields')->protectFields('field_1', 'field_2');
        $this->addType('multi_protected_fields')->protectFields('field_1', 'field_2')->protectFields('', 'field_8')->protectFields('field_2', 'field_3')->unprotectFields('field_8');
    }
}
