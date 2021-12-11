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

class OnlyOneTraitTest extends TestCase
{
    public function testUniqueAutomaticallyAddsIndex()
    {
        $not_only_one = new NameField();
        $this->assertFalse($not_only_one->getAddIndex());

        $only_one = (new NameField())->onlyOne('123');
        $this->assertTrue($only_one->getAddIndex());
        $this->assertEquals(IndexInterface::UNIQUE, $only_one->getAddIndexType());
    }

    public function testIndexUsesUniqueContext()
    {
        $only_one = (new NameField())->onlyOne('123');
        $this->assertTrue($only_one->getAddIndex());
        $this->assertSame([], $only_one->getAddIndexContext());

        $only_one = (new NameField())->onlyOne('one', 'two', 'three');
        $this->assertTrue($only_one->getAddIndex());
        $this->assertSame(['one', 'two', 'three'], $only_one->getAddIndexContext());
    }
}