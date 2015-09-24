<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Index;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class NameFieldTest extends TestCase
{
    /**
     * Test if name is default field name
     */
    public function testDefaultName()
    {
        $this->assertEquals('name', (new NameField())->getName());
    }

    /**
     * Test if NULL is the default value
     */
    public function testNullIsDefaultValue()
    {
        $this->assertNull((new NameField())->getDefaultValue());
    }

    /**
     * Test if name uses trim() as value modifier by default
     */
    public function testNameIsTrimmedByDefault()
    {
        $this->assertEquals('trim', (new NameField())->getModifier());
    }

    /**
     * Test if name can be added to a type
     */
    public function testNameCanBeAddedToType()
    {
        $type = (new Type('writers'))->addField(new NameField());

        $this->assertArrayHasKey('name', $type->getFields());
        $this->assertInstanceOf(NameField::class, $type->getFields()['name']);
    }

    /**
     * Test if name field does not add an index by default
     */
    public function testNameFieldDoesNotAddIndexByDefault()
    {
        $type = (new Type('writers'))->addField(new NameField());

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if name index is added to the type when requested
     */
    public function testNameFieldAddsIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField(new NameField('name', null, true));

        $this->assertArrayHasKey('name', $type->getIndexes());
        $this->assertInstanceOf(IndexInterface::class, $type->getIndexes()['name']);
    }
}