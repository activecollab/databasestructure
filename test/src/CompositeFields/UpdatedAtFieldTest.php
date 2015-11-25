<?php

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface;
use ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface\Implementation as UpdatedAtInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Composite\UpdatedAtField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test\CompositeFields
 */
class UpdatedAtFieldTest extends TestCase
{
    /**
     * Test if updated_at is default field name
     */
    public function testDefaultName()
    {
        $this->assertEquals('updated_at', (new UpdatedAtField())->getName());
    }

    /**
     * Test if updated_at can be added to a type
     */
    public function testUpdatedAtCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new UpdatedAtField());

        $this->assertArrayHasKey('updated_at', $type->getFields());
        $this->assertInstanceOf(UpdatedAtField::class, $type->getFields()['updated_at']);
    }

    /**
     * Test if updated_at field adds index by default
     */
    public function testUpdatedAtAddsIndexByDefault()
    {
        $type = (new Type('chapters'))->addField(new UpdatedAtField());

        $this->assertArrayHasKey('updated_at', $type->getIndexes());
        $this->assertInstanceOf(Index::class, $type->getIndexes()['updated_at']);
    }

    /**
     * Test if updated_at field can be set so it does not add an index
     */
    public function testIndexCreationCanBeSkipped()
    {
        $type = (new Type('chapters'))->addField(new UpdatedAtField('updated_at', false));

        $this->assertArrayNotHasKey('updated_at', $type->getIndexes());
    }

    /**
     * Test if updated_at field adds behaviour to the type
     */
    public function testUpdatedAtFieldAddsBehaviourToType()
    {
        $type = (new Type('chapters'))->addField(new UpdatedAtField());

        $this->assertArrayHasKey(UpdatedAtInterface::class, $type->getTraits());
        $this->assertContains(UpdatedAtInterfaceImplementation::class, $type->getTraits()[UpdatedAtInterface::class]);
    }
}