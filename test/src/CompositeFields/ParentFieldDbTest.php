<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Behaviour\ParentOptionalInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ParentRequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\ParentField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Type;
use InvalidArgumentException;

class ParentFieldDbTest extends DbTestCase
{
    public function testNameMustNotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        new ParentField('');
    }

    public function testNameMustEndWithId()
    {
        $this->expectException(InvalidArgumentException::class);

        new ParentField('wooops');
    }

    public function testOnlyIdAsFieldNameIsNotAccepted()
    {
        $this->expectException(InvalidArgumentException::class);

        new ParentField('_id');
    }

    /**
     * Test default field name.
     */
    public function testDefaultName()
    {
        $this->assertEquals('parent_id', (new ParentField())->getName());
        $this->assertEquals('parent', (new ParentField())->getRelationName());
    }

    /**
     * Test if field can be added to type.
     */
    public function testFieldCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new ParentField());

        $this->assertArrayHasKey('parent_id', $type->getFields());
        $this->assertInstanceOf(ParentField::class, $type->getFields()['parent_id']);
    }

    /**
     * Test if parent field adds two fields to type.
     */
    public function testFieldAddsTwoFieldsToType()
    {
        $parent_id = new ParentField();

        $fields = $parent_id->getFields();

        $this->assertIsArray($fields);
        $this->assertCount(2, $fields);

        $this->assertInstanceOf(StringField::class, $fields[0]);
        $this->assertInstanceOf(IntegerField::class, $fields[1]);

        $type = (new Type('chapters'))->addField($parent_id);

        $this->assertArrayHasKey('parent_type', $type->getAllFields());
        $this->assertInstanceOf(StringField::class, $type->getAllFields()['parent_type']);

        $this->assertArrayHasKey('parent_id', $type->getAllFields());
        $this->assertInstanceOf(IntegerField::class, $type->getAllFields()['parent_id']);
    }

    /**
     * Test if parent_id field is required by default.
     */
    public function testParentIdFieldIsNotRequiredByDefault()
    {
        $type = (new Type('chapters'))->addField(new ParentField());

        /** @var ParentField $parent_id */
        $parent_id = $type->getFields()['parent_id'];

        $this->assertInstanceOf(ParentField::class, $parent_id);
        $this->assertFalse($parent_id->isRequired());

        /** @var IntegerField $id_field */
        $id_field = $type->getAllFields()['parent_id'];

        $this->assertInstanceOf(IntegerField::class, $id_field);
        $this->assertFalse($id_field->isRequired());
    }

    /**
     * Test if parent field can be set as required.
     */
    public function testParentIdFieldCanBeSetAsRequired()
    {
        $type = (new Type('chapters'))->addField((new ParentField())->required());

        /** @var ParentField $parent_id */
        $parent_id = $type->getFields()['parent_id'];

        $this->assertInstanceOf(ParentField::class, $parent_id);
        $this->assertTrue($parent_id->isRequired());

        /** @var IntegerField $id_field */
        $id_field = $type->getAllFields()['parent_id'];

        $this->assertInstanceOf(IntegerField::class, $id_field);
        $this->assertTrue($id_field->isRequired());
    }

    /**
     * Check if required adds required interface.
     */
    public function testRequiredAddsRequiedInterface()
    {
        $type = (new Type('chapters'))->addField((new ParentField())->required());

        $this->assertArrayHasKey(ParentRequiredInterface::class, $type->getTraits());
        $this->assertArrayNotHasKey(ParentOptionalInterface::class, $type->getTraits());
    }

    /**
     * Check if optional adds optional interface.
     */
    public function testOptionaAddsOptionalInterface()
    {
        $type = (new Type('chapters'))->addField(new ParentField());

        $this->assertArrayNotHasKey(ParentRequiredInterface::class, $type->getTraits());
        $this->assertArrayHasKey(ParentOptionalInterface::class, $type->getTraits());
    }

    /**
     * Test default size is normal.
     */
    public function testDefaultSizeIsNormal()
    {
        $parent_field = new ParentField();

        $this->assertEquals(FieldInterface::SIZE_NORMAL, $parent_field->getSize());
        $this->assertEquals(FieldInterface::SIZE_NORMAL, $parent_field->getFields()[1]->getSize());
    }

    /**
     * Test size can be changed.
     */
    public function testSizeCanBeChanged()
    {
        $parent_field = (new ParentField())->size(FieldInterface::SIZE_TINY);

        $this->assertEquals(FieldInterface::SIZE_TINY, $parent_field->getSize());
        $this->assertEquals(FieldInterface::SIZE_TINY, $parent_field->getFields()[1]->getSize());
    }

    /**
     * Test if index is automatically added to the type.
     */
    public function testActionByAddsIndexByDefault()
    {
        $type = (new Type('chapters'))->addField(new ParentField());

        $this->assertArrayHasKey('parent', $type->getIndexes());

        $parent_index = $type->getIndexes()['parent'];

        $this->assertInstanceOf(Index::class, $parent_index);
        $this->assertEquals(['parent_type', 'parent_id'], $parent_index->getFields());

        $this->assertArrayHasKey('parent_id', $type->getIndexes());

        $parent_id_index = $type->getIndexes()['parent_id'];
        $this->assertInstanceOf(Index::class, $parent_id_index);
        $this->assertEquals(['parent_id'], $parent_id_index->getFields());
    }

    /**
     * Test if we can skip index creation.
     */
    public function testIndexCreationCanBeSkipped()
    {
        $type = (new Type('chapters'))->addField(new ParentField('parent_id', false));

        $this->assertArrayNotHasKey('parent', $type->getIndexes());
        $this->assertArrayNotHasKey('parent_id', $type->getIndexes());
    }

    /**
     * Test if parent type and ID are automatically added to serialization list.
     */
    public function testFieldShouldSerializeTypeAndId()
    {
        $type = (new Type('chapters'))->addField(new ParentField());

        $this->assertContains('parent_type', $type->getSerialize());
        $this->assertContains('parent_id', $type->getSerialize());
    }
}
