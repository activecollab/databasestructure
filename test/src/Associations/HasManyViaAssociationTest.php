<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\HasManyViaAssociation;
use ActiveCollab\DatabaseStructure\Test\TestCase;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class HasManyViaAssociationTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value '' is not a valid type name
     */
    public function testIntermediaryTypeNameIsRequired()
    {
        new HasManyViaAssociation('users', '');
    }

    /**
     * Test target type name is name of the association by default.
     */
    public function testTargetTypeNameIsAssociationName()
    {
        $this->assertEquals('users', (new HasManyViaAssociation('users', 'user_accounts'))->getTargetTypeName());
    }
}
