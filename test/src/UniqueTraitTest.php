<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class UniqueTraitTest extends TestCase
{
    /**
     * Test unique automatically adds index.
     */
    public function testUniqueAutomaticallyAddsIndex()
    {
        $non_unique = new NameField();
        $this->assertFalse($non_unique->getAddIndex());

        $unique = (new NameField())->unique();
        $this->assertTrue($unique->getAddIndex());
        $this->assertEquals(IndexInterface::UNIQUE, $unique->getAddIndexType());
    }

    /**
     * Test if index inehrits key's unique context.
     */
    public function testIndexUsesUniqueContext()
    {
        $unique = (new NameField())->unique();
        $this->assertTrue($unique->getAddIndex());
        $this->assertSame([], $unique->getAddIndexContext());

        $unique = (new NameField())->unique('one', 'two', 'three');
        $this->assertTrue($unique->getAddIndex());
        $this->assertSame(['one', 'two', 'three'], $unique->getAddIndexContext());
    }
}
