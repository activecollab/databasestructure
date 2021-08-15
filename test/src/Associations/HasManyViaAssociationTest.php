<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\HasManyViaAssociation;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use InvalidArgumentException;

class HasManyViaAssociationTest extends TestCase
{
    public function testIntermediaryTypeNameIsRequired()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Value '' is not a valid type name");
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
