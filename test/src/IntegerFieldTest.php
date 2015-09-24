<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\NumberField;
use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class IntegerFieldTest extends TestCase
{
    /**
     * Test if integer field extends number
     */
    public function testIntegerExtendsNumber()
    {
        $this->assertTrue((new \ReflectionClass(IntegerField::class))->isSubclassOf(NumberField::class));
    }

    /**
     * Test if integer fields are not unsigned by default
     */
    public function testNotUnsignedByDefault()
    {
        $this->assertFalse((new IntegerField('int'))->getUnsigned());
    }

    /**
     * Test if integer fields can be set to be unsigned
     */
    public function testFieldCanBeSetToBeUnsigned()
    {
        $this->assertTrue((new IntegerField('int'))->unsigned(true)->getUnsigned());
    }

    /**
     * Check if size is normal by default
     */
    public function testSizeIsNormalByDefault()
    {
        $this->assertEquals(FieldInterface::SIZE_NORMAL, (new IntegerField('int'))->getSize());
    }

    /**
     * Test if size can be changed
     */
    public function testSizeCanBeChanged()
    {
        $this->assertEquals(FieldInterface::SIZE_TINY, (new IntegerField('int'))->size(FieldInterface::SIZE_TINY)->getSize());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionOnIncorrectSize()
    {
        (new IntegerField('int'))->size('Invalid Value');
    }
}