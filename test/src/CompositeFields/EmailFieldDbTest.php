<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Field\Composite\EmailField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Type;

class EmailFieldDbTest extends DbTestCase
{
    /**
     * Test if name can be set.
     */
    public function testName()
    {
        $this->assertEquals('email_address', (new EmailField('email_address'))->getName());
    }

    /**
     * Test if NULL is the default value.
     */
    public function testNullIsDefaultValue()
    {
        $this->assertNull((new EmailField('email_address'))->getDefaultValue());
    }

    /**
     * Test no modifier when default email address is NULL.
     */
    public function testNoModifierWhenNullIsDefaultValue()
    {
        $this->assertNull((new EmailField('email_address'))->getModifier());
    }

    /**
     * Test if email is trimmed when not null by default.
     */
    public function testValueIsTrimmedByDefault()
    {
        $this->assertEquals('trim', (new EmailField('email_address', 'not null'))->getModifier());
    }

    /**
     * Test if email can be added to a type.
     */
    public function testEmailCanBeAddedToType()
    {
        $type = (new Type('writers'))->addField(new EmailField('email_address'));

        $this->assertArrayHasKey('email_address', $type->getFields());
        $this->assertInstanceOf(EmailField::class, $type->getFields()['email_address']);
    }

    /**
     * Test if email field does not add an index by default.
     */
    public function testEmailFieldDoesNotAddIndexByDefault()
    {
        $type = (new Type('writers'))->addField(new EmailField('email_address'));

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if email index is added to the type when requested.
     */
    public function testEmailFieldAddsIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField(new EmailField('email_address', null, true));

        $this->assertArrayHasKey('email_address', $type->getIndexes());

        $email_address_index = $type->getIndexes()['email_address'];

        $this->assertInstanceOf(IndexInterface::class, $email_address_index);
        $this->assertEquals(IndexInterface::INDEX, $email_address_index->getIndexType());
    }

    /**
     * Test if unique email index is added to the type when requested.
     */
    public function testEmailFieldAddsUniqueIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField((new EmailField('email_address', null, true))->unique());

        $this->assertArrayHasKey('email_address', $type->getIndexes());

        $email_address_index = $type->getIndexes()['email_address'];

        $this->assertInstanceOf(IndexInterface::class, $email_address_index);
        $this->assertEquals(IndexInterface::UNIQUE, $email_address_index->getIndexType());
    }

    /**
     * Test if unique index properly alters index added by the email field.
     */
    public function testUniqueContextAddsFieldsToTheIndex()
    {
        $type = (new Type('applications'))->addField((new EmailField('email_address', null, true))->unique('application_id', 'shard_id'));

        $this->assertArrayHasKey('email_address', $type->getIndexes());

        $name_index = $type->getIndexes()['email_address'];

        $this->assertInstanceOf(IndexInterface::class, $name_index);

        $this->assertEquals('email_address', $name_index->getName());
        $this->assertEquals(['email_address', 'application_id', 'shard_id'], $name_index->getFields());
    }

    /**
     * Make sure that resulting string field is required and / or unique when name is required and / or unique.
     */
    public function testEmailProducesRequiredAndUniqueStringWhenRequiredAndUnique()
    {
        $email_field = (new EmailField('email_address', ''))
            ->required()
            ->unique('context_filed_1');

        /** @var StringField $string_field */
        $string_field = $email_field->getFields()[0];

        $this->assertInstanceOf(StringField::class, $string_field);

        $this->assertTrue($string_field->isRequired());
        $this->assertTrue($string_field->isUnique());
        $this->assertEquals(['context_filed_1'], $string_field->getUniquenessContext());
    }
}
