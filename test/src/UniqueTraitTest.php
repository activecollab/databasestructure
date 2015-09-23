<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Composite\Name;
use ActiveCollab\DatabaseStructure\Index;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class UniqueTraitTest extends TestCase
{
    /**
     * Test unique automatically adds index
     */
    public function testUniqueAutomaticallyAddsIndex()
    {
        $non_unique = new Name();
        $this->assertFalse($non_unique->getAddIndex());

        $unique = (new Name())->unique();
        $this->assertTrue($unique->getAddIndex());
        $this->assertEquals(Index::UNIQUE, $unique->getAddIndexType());
    }

    /**
     * Test if index inehrits key's unique context
     */
    public function testIndexUsesUniqueContext()
    {
        $unique = (new Name())->unique();
        $this->assertTrue($unique->getAddIndex());
        $this->assertSame([], $unique->getAddIndexContext());

        $unique = (new Name())->unique('one', 'two', 'three');
        $this->assertTrue($unique->getAddIndex());
        $this->assertSame(['one', 'two', 'three'], $unique->getAddIndexContext());
    }
}