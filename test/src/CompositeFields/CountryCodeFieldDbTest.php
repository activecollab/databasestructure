<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Field\Composite\CountryCodeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Type;

class CountryCodeFieldDbTest extends DbTestCase
{
    /**
     * Test default field name.
     */
    public function testDefaultName()
    {
        $this->assertEquals('country_code', (new CountryCodeField())->getName());
    }

    /**
     * Test if name can be set.
     */
    public function testName()
    {
        $this->assertEquals('country', (new CountryCodeField('country'))->getName());
    }

    public function testLength()
    {
        /** @var StringField $string_field */
        $string_field = (new CountryCodeField())->getFields()[0];

        $this->assertEquals(2, $string_field->getLength());
    }

    /**
     * Test if NULL is the default value.
     */
    public function testNullIsDefaultValue()
    {
        $this->assertNull((new CountryCodeField())->getDefaultValue());
    }

    /**
     * Test no modifier when default email address is NULL.
     */
    public function testNoModifierWhenNullIsDefaultValue()
    {
        $this->assertNull((new CountryCodeField())->getModifier());
    }

    /**
     * Test if country code value is trimmed when not null by default.
     */
    public function testValueIsUppercasedByDefault()
    {
        $this->assertEquals('strtoupper', (new CountryCodeField('country_code', 'DE'))->getModifier());
    }

    /**
     * Test if country code field can be added to a type.
     */
    public function testCountryCodeCanBeAddedToType()
    {
        $type = (new Type('writers'))->addField(new CountryCodeField());

        $this->assertArrayHasKey('country_code', $type->getFields());
        $this->assertInstanceOf(CountryCodeField::class, $type->getFields()['country_code']);
    }

    /**
     * Test if country code field does not add an index by default.
     */
    public function testCountryCodeFieldDoesNotAddIndexByDefault()
    {
        $type = (new Type('writers'))->addField(new CountryCodeField());

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if country code index is added to the type when requested.
     */
    public function testCountryCodeFieldAddsIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField(new CountryCodeField('country_code', null, true));

        $this->assertArrayHasKey('country_code', $type->getIndexes());

        $country_code_index = $type->getIndexes()['country_code'];

        $this->assertInstanceOf(IndexInterface::class, $country_code_index);
        $this->assertEquals(IndexInterface::INDEX, $country_code_index->getIndexType());
    }

    /**
     * Test if unique country code index is added to the type when requested.
     */
    public function testEmailFieldAddsUniqueIndexWhenRequested()
    {
        $type = (new Type('writers'))->addField((new CountryCodeField('country_code', null, true))->unique());

        $this->assertArrayHasKey('country_code', $type->getIndexes());

        $country_code_index = $type->getIndexes()['country_code'];

        $this->assertInstanceOf(IndexInterface::class, $country_code_index);
        $this->assertEquals(IndexInterface::UNIQUE, $country_code_index->getIndexType());
    }

    /**
     * Test if unique index properly alters index added by the email field.
     */
    public function testUniqueContextAddsFieldsToTheIndex()
    {
        $type = (new Type('applications'))->addField((new CountryCodeField('country_code', null, true))->unique('application_id', 'shard_id'));

        $this->assertArrayHasKey('country_code', $type->getIndexes());

        $name_index = $type->getIndexes()['country_code'];

        $this->assertInstanceOf(IndexInterface::class, $name_index);

        $this->assertEquals('country_code', $name_index->getName());
        $this->assertEquals(['country_code', 'application_id', 'shard_id'], $name_index->getFields());
    }

    /**
     * Make sure that resulting string field is required and / or unique when name is required and / or unique.
     */
    public function testCountryCodeProducesRequiredAndUniqueStringWhenRequiredAndUnique()
    {
        $country_code_field = (new CountryCodeField('country_code', ''))
            ->required()
            ->unique('context_filed_1');

        /** @var StringField $string_field */
        $string_field = $country_code_field->getFields()[0];

        $this->assertInstanceOf(StringField::class, $string_field);

        $this->assertTrue($string_field->isRequired());
        $this->assertTrue($string_field->isUnique());
        $this->assertEquals(['context_filed_1'], $string_field->getUniquenessContext());
    }
}
