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
    public function testOnlyOneAutomaticallyAddsIndex(): void
    {
        $not_only_one = new NameField();
        $this->assertFalse($not_only_one->getAddIndex());

        $only_one = (new NameField())->onlyOne('123');
        $this->assertTrue($only_one->getAddIndex());
        $this->assertEquals(IndexInterface::INDEX, $only_one->getAddIndexType());
    }

    public function testIndexUsesOnlyOneContext(): void
    {
        $only_one = (new NameField())->onlyOne('123');
        $this->assertTrue($only_one->getAddIndex());
        $this->assertSame([], $only_one->getAddIndexContext());

        $only_one = (new NameField())->onlyOne('123', 'one', 'two', 'three');
        $this->assertTrue($only_one->getAddIndex());
        $this->assertSame(['one', 'two', 'three'], $only_one->getAddIndexContext());
    }

    public function testOnlyOneWillNotOverrideExistingIndex(): void
    {
        $unique_and_only_one = (new NameField())->unique()->onlyOne('123');
        $this->assertEquals(IndexInterface::UNIQUE, $unique_and_only_one->getAddIndexType());
    }
}