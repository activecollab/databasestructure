<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\Integer;
use ActiveCollab\DatabaseStructure\Field\Scalar\Number;
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
        $this->assertTrue((new \ReflectionClass(Integer::class))->isSubclassOf(Number::class));
    }

    /**
     * Test if integer fields are not unsigned by default
     */
    public function testNotUnsignedByDefault()
    {
        $this->assertFalse((new Integer('int'))->getUnsigned());
    }

    /**
     * Test if integer fields can be set to be unsigned
     */
    public function testFieldCanBeSetToBeUnsigned()
    {
        $this->assertTrue((new Integer('int'))->unsigned(true)->getUnsigned());
    }

    /**
     * Check if size is normal by default
     */
    public function testSizeIsNormalByDefault()
    {
        $this->assertEquals(FieldInterface::SIZE_NORMAL, (new Integer('int'))->getSize());
    }

    /**
     * Test if size can be changed
     */
    public function testSizeCanBeChanged()
    {
        $this->assertEquals(FieldInterface::SIZE_TINY, (new Integer('int'))->size(FieldInterface::SIZE_TINY)->getSize());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionOnIncorrectSize()
    {
        (new Integer('int'))->size('Invalid Value');
    }
}