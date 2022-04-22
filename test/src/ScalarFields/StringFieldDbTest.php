<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use InvalidArgumentException;

class StringFieldDbTest extends DbTestCase
{
    public function testExceptionWhenModifierFunctionDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);

        (new StringField('name'))->modifier('this function does not exist');
    }

    /**
     * Test if modifier is set when a valid function name is provided.
     */
    public function testModifierCanBeSet()
    {
        $field = (new StringField('name'))->modifier('trim');
        $this->assertEquals('trim', $field->getModifier());
    }

    /**
     * Check if length defaults to 191.
     */
    public function testLengthIs191ByDefault()
    {
        $this->assertEquals(191, (new StringField('some_string'))->getLength());
    }

    /**
     * Check if length can be changed.
     */
    public function testLengthCanBeChanged()
    {
        $this->assertEquals(15, (new StringField('some_string'))->length(15)->getLength());
    }

    public function testExceptionOnLengthToSmall()
    {
        $this->expectException(InvalidArgumentException::class);

        (new StringField('some_string'))->length(-1);
    }

    public function testExceptionOnLengthToLarge()
    {
        $this->expectException(InvalidArgumentException::class);

        (new StringField('some_string'))->length(255);
    }

    public function testAddIndex()
    {
        $string_field = new StringField('some_string', '', true);

        $this->assertInstanceOf(AddIndexInterface::class, $string_field);
        $this->assertTrue($string_field->getAddIndex());
    }

    public function testUniqueAddsIndex()
    {
        $string_field = (new StringField('some_string'))->unique('field_1', 'field_2');

        $this->assertTrue($string_field->getAddIndex());
        $this->assertSame(IndexInterface::UNIQUE, $string_field->getAddIndexType());
        $this->assertSame(['field_1', 'field_2'], $string_field->getAddIndexContext());
    }
}
