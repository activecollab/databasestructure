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
}