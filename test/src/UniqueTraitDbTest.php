<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;

class UniqueTraitDbTest extends DbTestCase
{
    public function testUniqueAutomaticallyAddsIndex()
    {
        $non_unique = new NameField();
        $this->assertFalse($non_unique->getAddIndex());

        $unique = (new NameField())->unique();
        $this->assertTrue($unique->getAddIndex());
        $this->assertEquals(IndexInterface::UNIQUE, $unique->getAddIndexType());
    }

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
