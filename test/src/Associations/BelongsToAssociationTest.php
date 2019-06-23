<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKeyField;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

class BelongsToAssociationTest extends TestCase
{
    /**
     * Test if target type name is pluralized association name by default.
     */
    public function testTargetTypeNameIsPluralizedByDefault()
    {
        $this->assertEquals('books', (new BelongsToAssociation('book'))->getTargetTypeName());
    }

    /**
     * Test if target type name can be specified.
     */
    public function testTargetTypeNameCanBeSpecified()
    {
        $this->assertEquals('awesome_books', (new BelongsToAssociation('book', 'awesome_books'))->getTargetTypeName());
    }

    /**
     * Test if belongs to associations are not optional by default.
     */
    public function testBelongsToIsRequiredByDefault()
    {
        $this->assertTrue((new BelongsToAssociation('book'))->isRequired());
    }

    /**
     * Test if belongs to association can be set as optional.
     */
    public function testBelongsToCanBeSetAsOptiona()
    {
        $this->assertFalse((new BelongsToAssociation('book'))->required(false)->isRequired());
    }

    /**
     * Test if BelongsToAssociation properly passes info whether it is required or not.
     */
    public function testBelongsToProperlyPassesRequiredToFk()
    {
        /** @var ForeignKeyField $fk_should_be_required */
        $fk_should_be_required = (new BelongsToAssociation('book'))->getFields()[0];

        $this->assertInstanceOf(ForeignKeyField::class, $fk_should_be_required);
        $this->assertTrue($fk_should_be_required->isRequired());

        /** @var ForeignKeyField $fk_should_not_be_required */
        $fk_should_not_be_required = (new BelongsToAssociation('book'))->required(false)->getFields()[0];

        $this->assertInstanceOf(ForeignKeyField::class, $fk_should_not_be_required);
        $this->assertFalse($fk_should_not_be_required->isRequired());
    }

    /**
     * Test constraint name for belongs to association.
     */
    public function testConstraintName()
    {
        $writers = new Type('writers');
        $writers->addAssociation(new HasManyAssociation('books'));

        $books = new Type('books');
        $book_writer = new BelongsToAssociation('writer');
        $books->addAssociation($book_writer);

        $this->assertEquals('book_writer_constraint', $book_writer->getVerboseConstraintName());
        $this->assertEquals('belongs_to_' . md5('book_writer_constraint'), $book_writer->getConstraintName());
    }
}
