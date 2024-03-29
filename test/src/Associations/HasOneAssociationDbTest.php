<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Association\HasOneAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKeyField;
use ActiveCollab\DatabaseStructure\Test\Fixtures\TestInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Type;
use InvalidArgumentException;
use stdClass;

class HasOneAssociationDbTest extends DbTestCase
{
    /**
     * Test if target type name is pluralized association name by default.
     */
    public function testTargetTypeNameIsPluralizedByDefault()
    {
        $this->assertEquals('books', (new HasOneAssociation('book'))->getTargetTypeName());
    }

    /**
     * Test if target type name can be specified.
     */
    public function testTargetTypeNameCanBeSpecified()
    {
        $this->assertEquals('awesome_books', (new HasOneAssociation('book', 'awesome_books'))->getTargetTypeName());
    }

    /**
     * Test if has one associations are not optional by default.
     */
    public function testHasOneIsRequiredByDefault()
    {
        $this->assertTrue((new HasOneAssociation('book'))->isRequired());
    }

    /**
     * Test if has one association can be set as optional.
     */
    public function testHasOneCanBeSetAsOptiona()
    {
        $this->assertFalse((new HasOneAssociation('book'))->required(false)->isRequired());
    }

    /**
     * @dataProvider invalidInterfacesProvider
     *
     * @param string $invalid_interface
     */
    public function testAcceptsRequiresInterface(string $invalid_interface)
    {
        $this->expectException(InvalidArgumentException::class);

        (new HasOneAssociation('book'))->accepts($invalid_interface);
    }

    public function invalidInterfacesProvider(): array
    {
        return [
            [stdClass::class],
            ['not an interface'],
        ];
    }

    public function testHasOneCanTypeHintDifferentReturnType()
    {
        $this->assertSame(TestInterface::class, (new HasOneAssociation('book'))->accepts(TestInterface::class)->getAccepts());
    }

    /**
     * Test if has one association properly passes info whether it is required or not.
     */
    public function testHasOneProperlyPassesRequiredToFk()
    {
        /** @var ForeignKeyField $fk_should_be_required */
        $fk_should_be_required = (new HasOneAssociation('book'))->getFields()[0];

        $this->assertInstanceOf(ForeignKeyField::class, $fk_should_be_required);
        $this->assertTrue($fk_should_be_required->isRequired());

        /** @var ForeignKeyField $fk_should_not_be_required */
        $fk_should_not_be_required = (new HasOneAssociation('book'))->required(false)->getFields()[0];

        $this->assertInstanceOf(ForeignKeyField::class, $fk_should_not_be_required);
        $this->assertFalse($fk_should_not_be_required->isRequired());
    }

    /**
     * Test constraint name for has one association.
     */
    public function testConstraintName()
    {
        $writers = new Type('writers');
        $writers->addAssociation(new HasManyAssociation('books'));

        $books = new Type('books');
        $book_writer = new HasOneAssociation('writer');
        $books->addAssociation($book_writer);

        $this->assertEquals('book_writer_constraint', $book_writer->getVerboseConstraintName());
        $this->assertEquals('has_one_' . md5('book_writer_constraint'), $book_writer->getConstraintName());
    }
}
