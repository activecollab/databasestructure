<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields\PositionField;

use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface\Implementation as PositionInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Composite\PositionField;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class PositionFieldTest extends TestCase
{
    /**
     * Test if position is default field name.
     */
    public function testDefaultName()
    {
        $this->assertEquals('position', (new PositionField())->getName());
    }

    /**
     * Test if 0 is the default value.
     */
    public function testNullIsDefaultValue()
    {
        $this->assertSame(0, (new PositionField())->getDefaultValue());
    }

    /**
     * Test if position can be added to a type.
     */
    public function testPositionCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new PositionField());

        $this->assertArrayHasKey('position', $type->getFields());
        $this->assertInstanceOf(PositionField::class, $type->getFields()['position']);
    }

    /**
     * Test if position index is added to the type when requested.
     */
    public function testPositionFieldAddsIndexByDefault()
    {
        $type = (new Type('chapters'))->addField(new PositionField('position'));

        $this->assertArrayHasKey('position', $type->getIndexes());
        $this->assertInstanceOf(IndexInterface::class, $type->getIndexes()['position']);
    }

    /**
     * Test if position field does not add an index by default.
     */
    public function testPositionFieldDoesNotAddIndexWhenRequested()
    {
        $type = (new Type('chapters'))->addField(new PositionField('position', 0, false));

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if position fields adds behaviour to the type.
     */
    public function testPositionFieldAddsBehaviourToType()
    {
        $type = (new Type('chapters'))->addField(new PositionField('position'));

        $this->assertArrayHasKey(PositionInterface::class, $type->getTraits());
        $this->assertContains(PositionInterfaceImplementation::class, $type->getTraits()[PositionInterface::class]);
    }

    /**
     * Test if position context is the entire data set by default.
     */
    public function testContextIsEmptyByDefault()
    {
        $context = (new PositionField())->getContext();

        $this->assertIsArray($context);
        $this->assertEmpty($context);
    }

    /**
     * Test if position context can be changed.
     */
    public function testContextCanBeChanged()
    {
        $context = (new PositionField())->context('field_1', 'field_2')->getContext();

        $this->assertIsArray($context);
        $this->assertCount(2, $context);
        $this->assertContains('field_1', $context);
        $this->assertContains('field_2', $context);
    }

    /**
     * Test if context can be called with no arguments.
     */
    public function testContextCanBeCalledWithNoArguments()
    {
        $context = (new PositionField())->context()->getContext();

        $this->assertIsArray($context);
        $this->assertEmpty($context);
    }
}
