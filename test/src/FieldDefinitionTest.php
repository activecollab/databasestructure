<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class FieldDefinitionTest extends TestCase
{
    /**
     * Test if scalar fields are added to the model by default
     */
    public function testScalarFieldsShouldBeAddedToModelByDefault()
    {
        $this->assertTrue((new StringField('is_important'))->getShouldBeAddedToModel());
    }

    /**
     * Test if scalar fields can be omitted from model
     */
    public function testScalarFieldsCanBeOmittedFromModel()
    {
        $this->assertFalse((new StringField('is_important'))->setShouldBeAddedToModel(false)->getShouldBeAddedToModel());
    }
}