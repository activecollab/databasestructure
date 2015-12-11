<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Association\HasManyViaAssociation;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class HasManyViaTest extends TestCase
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
     * Test target type name is name of the association by default
     */
    public function testTargetTypeNameIsAssociationName()
    {
        $this->assertEquals('users', (new HasManyViaAssociation('users', 'user_accounts'))->getTargetTypeName());
    }
}
