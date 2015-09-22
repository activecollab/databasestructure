<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\String;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class StringFieldTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenModifierFunctionDoesNotExist()
    {
        (new String('name'))->modifier('this function does not exist');
    }

    /**
     * Test if modifier is set when a valid function name is provided
     */
    public function testModifierCanBeSet()
    {
        $field = (new String('name'))->modifier('trim');
        $this->assertEquals('trim', $field->getModifier());
    }

    /**
     * Check if length defaults to 191
     */
    public function testLengthIs191ByDefault()
    {
        $this->assertEquals(191, (new String('some_string'))->getLength());
    }

    /**
     * Check if length can be changed
     */
    public function testLengthCanBeChanged()
    {
        $this->assertEquals(15, (new String('some_string'))->length(15)->getLength());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionOnLengthToSmall()
    {
        (new String('some_string'))->length(-1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionOnLengthToLarge()
    {
        (new String('some_string'))->length(255);
    }
}