<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Association\BelongsTo;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class BelongsToAssociationTest extends TestCase
{
    /**
     * Test if belongs to associations are not optional by default
     */
    public function testBelongsToIsNotOptionalByDefault()
    {
        $this->assertFalse((new BelongsTo('book'))->getOptional());
    }

    /**
     * Test if belongs to association can be set as optional
     */
    public function testBelongsToCanBeSetAsOptiona()
    {
        $this->assertTrue((new BelongsTo('book'))->optional(true)->getOptional());
    }
}