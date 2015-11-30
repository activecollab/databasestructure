<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Association\HasOneAssociation;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class HasOneAssociationTest extends TestCase
{
    /**
     * Test if target type name is pluralized association name by default
     */
    public function testTargetTypeNameIsPluralizedByDefault()
    {
        $this->assertEquals('books', (new HasOneAssociation('book'))->getTargetTypeName());
    }

    /**
     * Test if target type name can be specified
     */
    public function testTargetTypeNameCanBeSpecified()
    {
        $this->assertEquals('awesome_books', (new HasOneAssociation('book', 'awesome_books'))->getTargetTypeName());
    }
}
