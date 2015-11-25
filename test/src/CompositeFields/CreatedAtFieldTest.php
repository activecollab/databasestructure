<?php

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface;
use ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface\Implementation as CreatedAtInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Composite\CreatedAtField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test\CompositeFields
 */
class CreatedAtFieldTest extends TestCase
{
    /**
     * Test if created_at is default field name
     */
    public function testDefaultName()
    {
        $this->assertEquals('created_at', (new CreatedAtField())->getName());
    }

    /**
     * Test if created_at can be added to a type
     */
    public function testCreatedAtCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new CreatedAtField());

        $this->assertArrayHasKey('created_at', $type->getFields());
        $this->assertInstanceOf(CreatedAtField::class, $type->getFields()['created_at']);
    }

    /**
     * Test if created_at field adds index by default
     */
    public function testCreatedAtAddsIndexByDefault()
    {
        $type = (new Type('chapters'))->addField(new CreatedAtField());

        $this->assertArrayHasKey('created_at', $type->getIndexes());
        $this->assertInstanceOf(Index::class, $type->getIndexes()['created_at']);
    }

    /**
     * Test if created_at field can be set so it does not add an index
     */
    public function testIndexCreationCanBeSkipped()
    {
        $type = (new Type('chapters'))->addField(new CreatedAtField('created_at', false));

        $this->assertArrayNotHasKey('created_at', $type->getIndexes());
    }

    /**
     * Test if created_at field adds behaviour to the type
     */
    public function testCreatedAtFieldAddsBehaviourToType()
    {
        $type = (new Type('chapters'))->addField(new CreatedAtField());

        $this->assertArrayHasKey(CreatedAtInterface::class, $type->getTraits());
        $this->assertContains(CreatedAtInterfaceImplementation::class, $type->getTraits()[CreatedAtInterface::class]);
    }

    /**
     * Test if field is automatically added to serialization list
     */
    public function testFieldShouldBeSerialized()
    {
        $this->assertContains('created_at', (new Type('chapters'))->addField(new CreatedAtField())->getSerialize());
    }
}