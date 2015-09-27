<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface;
use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface\Implementation as AdditionalPropertiesInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Composite\AdditionalPropertiesField;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class AdditionalProprtiesFieldTest extends TestCase
{
    /**
     * Test if raw_additional_properties is default field name
     */
    public function testDefaultName()
    {
        $this->assertEquals('raw_additional_properties', (new AdditionalPropertiesField())->getName());
    }

    /**
     * Test if 0 is the default value
     */
    public function testNullIsDefaultValue()
    {
        $this->assertNull((new AdditionalPropertiesField())->getDefaultValue());
    }

    /**
     * Test if additinal properties field can be added to a type
     */
    public function testAdditionalPropertiesCanBeAddedToType()
    {
        $type = (new Type('chapters'))->addField(new AdditionalPropertiesField());

        $this->assertArrayHasKey('raw_additional_properties', $type->getFields());
        $this->assertInstanceOf(AdditionalPropertiesField::class, $type->getFields()['raw_additional_properties']);
    }

    /**
     * Test if additional properties field adds behaviour to the type
     */
    public function testAdditionalPropertiesFieldAddsBehaviourToType()
    {
        $type = (new Type('chapters'))->addField(new AdditionalPropertiesField());

        $this->assertArrayHasKey(AdditionalPropertiesInterface::class, $type->getTraits());
        $this->assertContains(AdditionalPropertiesInterfaceImplementation::class, $type->getTraits()[AdditionalPropertiesInterface::class]);
    }
}