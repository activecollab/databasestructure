<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\NumberField;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use InvalidArgumentException;

class IntegerFieldDbTest extends DbTestCase
{
    /**
     * Test if integer field extends number.
     */
    public function testIntegerExtendsNumber()
    {
        $this->assertTrue((new \ReflectionClass(IntegerField::class))->isSubclassOf(NumberField::class));
    }

    /**
     * Test if integer fields are not unsigned by default.
     */
    public function testNotUnsignedByDefault()
    {
        $this->assertFalse((new IntegerField('int'))->isUnsigned());
    }

    /**
     * Test if integer fields can be set to be unsigned.
     */
    public function testFieldCanBeSetToBeUnsigned()
    {
        $this->assertTrue((new IntegerField('int'))->unsigned(true)->isUnsigned());
    }

    /**
     * Check if size is normal by default.
     */
    public function testSizeIsNormalByDefault()
    {
        $this->assertEquals(FieldInterface::SIZE_NORMAL, (new IntegerField('int'))->getSize());
    }

    /**
     * Test if size can be changed.
     */
    public function testSizeCanBeChanged()
    {
        $this->assertEquals(FieldInterface::SIZE_TINY, (new IntegerField('int'))->size(FieldInterface::SIZE_TINY)->getSize());
    }

    public function testExceptionOnIncorrectSize()
    {
        $this->expectException(InvalidArgumentException::class);

        (new IntegerField('int'))->size('Invalid Value');
    }
}
