<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Behaviour\CreatedByOptionalInterface;
use ActiveCollab\DatabaseStructure\Behaviour\CreatedByRequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\CreatedByField;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

class CreatedByFieldTest extends TestCase
{
    /**
     * Test default name.
     */
    public function testDefaultName()
    {
        $this->assertEquals('created_by_id', (new CreatedByField('User'))->getName());
    }

    /**
     * Check if required adds required interface.
     */
    public function testRequiredAddsRequiedInterface()
    {
        $type = (new Type('chapters'))->addField((new CreatedByField('User'))
            ->required());

        $this->assertArrayHasKey(CreatedByRequiredInterface::class, $type->getTraits());
        $this->assertArrayNotHasKey(CreatedByOptionalInterface::class, $type->getTraits());
    }

    /**
     * Check if optional adds optional interface.
     */
    public function testOptionaAddsOptionalInterface()
    {
        $type = (new Type('chapters'))->addField(new CreatedByField('User'));

        $this->assertArrayNotHasKey(CreatedByRequiredInterface::class, $type->getTraits());
        $this->assertArrayHasKey(CreatedByOptionalInterface::class, $type->getTraits());
    }
}
