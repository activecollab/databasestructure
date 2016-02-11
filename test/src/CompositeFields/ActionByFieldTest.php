<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Field\Composite\ActionByField;
use ActiveCollab\DatabaseStructure\Field\Composite\EmailField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test\CompositeFields
 */
class ActionByFieldTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNameMustNotBeEmpty()
    {
        new ActionByField('', 'User', 'AnonymousUser');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNameMustEndWithById()
    {
        new ActionByField('wooops', 'User', 'AnonymousUser');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserClassMustNotBeEmpty()
    {
        new ActionByField('wooops', '', 'AnonymousUser');
    }

    /**
     * Test if we use full user class name.
     */
    public function testUserClassUsesFullPath()
    {
        $this->assertEquals('\User', (new ActionByField('created_by_id', 'User', 'AnonymousUser'))->getUserClassName());
        $this->assertEquals('\User', (new ActionByField('created_by_id', '\User', 'AnonymousUser'))->getUserClassName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAnonymousUserClassMustNotBeEmpty()
    {
        new ActionByField('wooops', 'User', '');
    }

    /**
     * Test if we use full anonymous user class name.
     */
    public function testAnonymousUserClassUsesFullPath()
    {
        $this->assertEquals('\AnonymousUser', (new ActionByField('created_by_id', 'User', 'AnonymousUser'))->getAnonymousUserClassName());
        $this->assertEquals('\AnonymousUser', (new ActionByField('created_by_id', 'User', '\AnonymousUser'))->getAnonymousUserClassName());
    }

    /**
     * Test if field can be added to type.
     */
    public function testFieldCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new ActionByField('created_by_id', 'User', 'AnonymousUser'));

        $this->assertArrayHasKey('created_by_id', $type->getFields());
        $this->assertInstanceOf(ActionByField::class, $type->getFields()['created_by_id']);
    }

    public function testFieldAddsThreeFieldsToType()
    {
        $created_by_id = new ActionByField('created_by_id', 'User', 'AnonymousUser');

        $fields = $created_by_id->getFields();

        $this->assertInternalType('array', $fields);
        $this->assertCount(3, $fields);

        $this->assertInstanceOf(IntegerField::class, $fields[0]);
        $this->assertInstanceOf(StringField::class, $fields[1]);
        $this->assertInstanceOf(EmailField::class, $fields[2]);

        $type = (new Type('chapters'))->addField($created_by_id);

        $this->assertArrayHasKey('created_by_id', $type->getAllFields());
        $this->assertInstanceOf(IntegerField::class, $type->getAllFields()['created_by_id']);

        $this->assertArrayHasKey('created_by_name', $type->getAllFields());
        $this->assertInstanceOf(StringField::class, $type->getAllFields()['created_by_name']);

        $this->assertArrayHasKey('created_by_email', $type->getAllFields());
        $this->assertInstanceOf(StringField::class, $type->getAllFields()['created_by_email']);
    }

    /**
     * Test if action_by_id field is required by default.
     */
    public function testActionByIdFieldIsNotRequiredByDefault()
    {
        $type = (new Type('chapters'))->addField(new ActionByField('created_by_id', 'User', 'AnonymousUser'));

        /** @var ActionByField $action_by */
        $action_by = $type->getFields()['created_by_id'];

        $this->assertInstanceOf(ActionByField::class, $action_by);
        $this->assertFalse($action_by->isRequired());

        /** @var IntegerField $created_by_id */
        $created_by_id = $type->getAllFields()['created_by_id'];

        $this->assertInstanceOf(IntegerField::class, $created_by_id);
        $this->assertFalse($created_by_id->isRequired());
    }

    /**
     * Test if action by field can be set as required.
     */
    public function testActionByIdFieldCanBeSetAsRequired()
    {
        $type = (new Type('chapters'))->addField((new ActionByField('created_by_id', 'User', 'AnonymousUser'))->required());

        /** @var ActionByField $action_by */
        $action_by = $type->getFields()['created_by_id'];

        $this->assertInstanceOf(ActionByField::class, $action_by);
        $this->assertTrue($action_by->isRequired());

        /** @var IntegerField $created_by_id */
        $created_by_id = $type->getAllFields()['created_by_id'];

        $this->assertInstanceOf(IntegerField::class, $created_by_id);
        $this->assertTrue($created_by_id->isRequired());
    }

    /**
     * Test if index is automatically added to the type.
     */
    public function testActionByAddsIndexByDefault()
    {
        $type = (new Type('chapters'))->addField(new ActionByField('created_by_id', 'User', 'AnonymousUser'));

        $this->assertArrayHasKey('created_by_id', $type->getIndexes());
        $this->assertInstanceOf(Index::class, $type->getIndexes()['created_by_id']);
    }

    /**
     * Test if we can skip index creation.
     */
    public function testIndexCreationCanBeSkipped()
    {
        $type = (new Type('chapters'))->addField(new ActionByField('created_by_id', 'User', 'AnonymousUser', false));

        $this->assertArrayNotHasKey('created_by_id', $type->getIndexes());
    }

    /**
     * Test if created by ID is automatically added to serialization list.
     */
    public function testFieldShouldSerializeId()
    {
        $this->assertContains('created_by_id', (new Type('chapters'))->addField(new ActionByField('created_by_id', 'User', 'AnonymousUser'))->getSerialize());
    }
}
