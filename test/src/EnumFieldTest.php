<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\EnumField;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class EnumFieldTest extends TestCase
{
    /**
     * Test if array of possibilities is empty by default.
     */
    public function testEmptyArrayOfPossibilitiesByDefault()
    {
        $default_possibilities = (new EnumField('one_of_many'))->getPossibilities();

        $this->assertInternalType('array', $default_possibilities);
        $this->assertCount(0, $default_possibilities);
    }

    /**
     * Test possibilities can be changed.
     */
    public function testPossibilitiesCanBeChanged()
    {
        $possibilities = (new EnumField('one_of_many'))->possibilities('one', 'two', 'three')->getPossibilities();

        $this->assertInternalType('array', $possibilities);
        $this->assertCount(3, $possibilities);
        $this->assertEquals(['one', 'two', 'three'], $possibilities);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDefaultValueNeedsToBeInPossibilities()
    {
        $one_of_many = new EnumField('one_of_many', 'default_one');
        $one_of_many->possibilities('one', 'two', 'three');
    }
}
