<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\EnumField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use InvalidArgumentException;

class EnumFieldDbTest extends DbTestCase
{
    /**
     * Test if array of possibilities is empty by default.
     */
    public function testEmptyArrayOfPossibilitiesByDefault()
    {
        $default_possibilities = (new EnumField('one_of_many'))->getPossibilities();

        $this->assertIsArray($default_possibilities);
        $this->assertCount(0, $default_possibilities);
    }

    /**
     * Test possibilities can be changed.
     */
    public function testPossibilitiesCanBeChanged()
    {
        $possibilities = (new EnumField('one_of_many'))
            ->possibilities('one', 'two', 'three')
            ->getPossibilities();

        $this->assertIsArray($possibilities);
        $this->assertCount(3, $possibilities);
        $this->assertEquals(['one', 'two', 'three'], $possibilities);
    }

    public function testDefaultValueNeedsToBeInPossibilities()
    {
        $this->expectException(InvalidArgumentException::class);
        $one_of_many = new EnumField('one_of_many', 'default_one');
        $one_of_many->possibilities('one', 'two', 'three');
    }

    public function testNativeType()
    {
        $this->assertSame('string', (new EnumField('one_of_many'))->possibilities('one', 'two', 'three')->getNativeType());
    }
}
