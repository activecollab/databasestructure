<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Field\Composite\Position;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface\Implementation as PositionInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class PositionFieldTest extends TestCase
{
    /**
     * Test if position is default field name
     */
    public function testDefaultName()
    {
        $this->assertEquals('position', (new Position())->getName());
    }

    /**
     * Test if 0 is the default value
     */
    public function testNullIsDefaultValue()
    {
        $this->assertSame(0, (new Position())->getDefaultValue());
    }
    /**
     * Test if position can be added to a type
     */
    public function testPositionCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new Position());

        $this->assertArrayHasKey('position', $type->getFields());
        $this->assertInstanceOf(Position::class, $type->getFields()['position']);
    }

    /**
     * Test if position index is added to the type when requested
     */
    public function testPositionFieldAddsIndexByDefault()
    {
        $type = (new Type('chapters'))->addField(new Position('position'));

        $this->assertArrayHasKey('position', $type->getIndexes());
        $this->assertInstanceOf(Index::class, $type->getIndexes()['position']);
    }

    /**
     * Test if position field does not add an index by default
     */
    public function testPositionFieldDoesNotAddIndexWhenRequested()
    {
        $type = (new Type('chapters'))->addField(new Position('position', 0, false));

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if position fields adds behaviour to the type
     */
    public function testPositionFieldAddsBehaviourToType()
    {
        $type = (new Type('chapters'))->addField(new Position('position'));

        $this->assertArrayHasKey(PositionInterface::class, $type->getTraits());
        $this->assertContains(PositionInterfaceImplementation::class, $type->getTraits()[PositionInterface::class]);
    }

    /**
     * Test if position context is the entire data set by default
     */
    public function testContextIsEmptyByDefault()
    {
        $context = (new Position())->getContext();

        $this->assertInternalType('array', $context);
        $this->assertEmpty($context);
    }

    /**
     * Test if position context can be changed
     */
    public function testContextCanBeChanged()
    {
        $context = (new Position())->context('field_1', 'field_2')->getContext();

        $this->assertInternalType('array', $context);
        $this->assertCount(2, $context);
        $this->assertContains('field_1', $context);
        $this->assertContains('field_2', $context);
    }

    /**
     * Test if context can be called with no arguments
     */
    public function testContextCanBeCalledWithNoArguments()
    {
        $context = (new Position())->context()->getContext();

        $this->assertInternalType('array', $context);
        $this->assertEmpty($context);
    }
}