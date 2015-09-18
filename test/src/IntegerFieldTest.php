<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\Integer;
use ActiveCollab\DatabaseStructure\Field\Scalar\Number;

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
        $this->assertTrue((new Integer('int'))->setUnsigned(true)->getUnsigned());
    }
}