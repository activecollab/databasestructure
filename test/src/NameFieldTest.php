<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;

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

    /**
     * Test if unique index properly alters index added by the name field
     */
    public function testUniqueContextAddsFieldsToTheIndex()
    {
        $type = (new Type('applications'))->addField((new NameField('name', null, true))->unique('application_id', 'shard_id'));

        $this->assertArrayHasKey('name', $type->getIndexes());

        $name_index = $type->getIndexes()['name'];

        $this->assertInstanceOf(IndexInterface::class, $name_index);

        $this->assertEquals('name', $name_index->getName());
        $this->assertEquals(['name', 'application_id', 'shard_id'], $name_index->getFields());
    }

    /**
     * Make sure that resulting string field is required and / or unique when name is required and / or unique
     */
    public function testNameProducesRequiredAndUniqueStringWhenRequiredAndUnique()
    {
        $name_field = (new NameField('name'))->required()->unique('context_filed_1');

        /** @var StringField $string_field */
        $string_field = $name_field->getFields()[0];

        $this->assertInstanceOf(StringField::class, $string_field);

        $this->assertTrue($string_field->isRequired());
        $this->assertTrue($string_field->isUnique());
        $this->assertEquals(['context_filed_1'], $string_field->getUniquenessContext());
    }
}