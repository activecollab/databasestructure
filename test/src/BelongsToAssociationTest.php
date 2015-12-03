<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class BelongsToAssociationTest extends TestCase
{
    /**
     * Test if target type name is pluralized association name by default
     */
    public function testTargetTypeNameIsPluralizedByDefault()
    {
        $this->assertEquals('books', (new BelongsToAssociation('book'))->getTargetTypeName());
    }

    /**
     * Test if target type name can be specified
     */
    public function testTargetTypeNameCanBeSpecified()
    {
        $this->assertEquals('awesome_books', (new BelongsToAssociation('book', 'awesome_books'))->getTargetTypeName());
    }

    /**
     * Test if belongs to associations are not optional by default
     */
    public function testBelongsToIsRequiredByDefault()
    {
        $this->assertTrue((new BelongsToAssociation('book'))->isRequired());
    }

    /**
     * Test if belongs to association can be set as optional
     */
    public function testBelongsToCanBeSetAsOptiona()
    {
        $this->assertFalse((new BelongsToAssociation('book'))->required(false)->isRequired());
    }

    /**
     * Test constraint name for belongs to association
     */
    public function testConstraintName()
    {
        $writers = new Type('writers');
        $writers->addAssociation(new HasManyAssociation('books'));

        $books = new Type('books');
        $book_writer = new BelongsToAssociation('writer');
        $books->addAssociation($book_writer);

        $this->assertEquals('book_writer_constraint', $book_writer->getConstraintName());
    }
}