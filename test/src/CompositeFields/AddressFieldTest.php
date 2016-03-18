<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Field\Composite\AddressField;
use ActiveCollab\DatabaseStructure\Field\Composite\CountryCodeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test\CompositeFields
 */
class AddressFieldTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name of the address field should be 'address' or end with '_address'
     */
    public function testErrorWhenFieldNameDoesNotEndWithAddress()
    {
        new AddressField('does not end with address');
    }

    /**
     * Test if address field can be named address.
     */
    public function testAddressNameWithoutPrefixIsAccepted()
    {
        $field = new AddressField('address');
        $this->assertEquals('', $field->getFieldNamePrefix());
    }

    /**
     * Test if address field name can be prefixed.
     */
    public function testAddressNameWIthPrefixIsAccepted()
    {
        $field = new AddressField('billing_address');
        $this->assertEquals('billing', $field->getFieldNamePrefix());
    }

    /**
     * Check field types and names when address field has no prefix.
     */
    public function testAddressCompositionFieldsWithoutPrefix()
    {
        $field = new AddressField('address');

        $this->assertCount(6, $field->getFields());

        $expectations = [
            'address' => StringField::class,
            'address_extended' => StringField::class,
            'city' => StringField::class,
            'zip_code' => StringField::class,
            'region' => StringField::class,
            'country_code' => CountryCodeField::class,
        ];

        $counter = 0;

        foreach ($expectations as $expected_field_name => $expected_field_type) {
            $this->assertInstanceOf($expected_field_type, $field->getFields()[$counter]);
            $this->assertEquals($expected_field_name, $field->getFields()[$counter]->getName());

            ++$counter;
        }
    }

    /**
     * Check field types and names when address field has a prefix.
     */
    public function testAddressCompositionFieldsWithPrefix()
    {
        $field = new AddressField('billing_address');

        $this->assertCount(6, $field->getFields());

        $expectations = [
            'billing_address' => StringField::class,
            'billing_address_extended' => StringField::class,
            'billing_city' => StringField::class,
            'billing_zip_code' => StringField::class,
            'billing_region' => StringField::class,
            'billing_country_code' => CountryCodeField::class,
        ];

        $counter = 0;

        foreach ($expectations as $expected_field_name => $expected_field_type) {
            $this->assertInstanceOf($expected_field_type, $field->getFields()[$counter]);
            $this->assertEquals($expected_field_name, $field->getFields()[$counter]->getName());

            ++$counter;
        }
    }

    /**
     * Test if indexes are not added by default.
     */
    public function testIndexesAreNotAddedByDefault()
    {
        $address_field = new AddressField();

        $this->assertFalse($address_field->getIndexOnCity());
        $this->assertFalse($address_field->getIndexOnZipCode());
        $this->assertFalse($address_field->getIndexOnRegion());
        $this->assertFalse($address_field->getIndexOnCountry());

        $type = (new Type('writers'))->addField($address_field);

        $this->assertCount(0, $type->getIndexes());
    }

    /**
     * Test if indexes can be added.
     */
    public function testIndexesCanBeAddedWhenSpecified()
    {
        $address_field = new AddressField('billing_address', true, true, true, true);

        $this->assertTrue($address_field->getIndexOnCity());
        $this->assertTrue($address_field->getIndexOnZipCode());
        $this->assertTrue($address_field->getIndexOnRegion());
        $this->assertTrue($address_field->getIndexOnCountry());

        $type = (new Type('writers'))->addField($address_field);

        $this->assertCount(4, $type->getIndexes());

        $this->assertInstanceOf(IndexInterface::class, $type->getIndexes()['billing_city']);
        $this->assertInstanceOf(IndexInterface::class, $type->getIndexes()['billing_zip_code']);
        $this->assertInstanceOf(IndexInterface::class, $type->getIndexes()['billing_region']);
        $this->assertInstanceOf(IndexInterface::class, $type->getIndexes()['billing_country']);
    }
}
