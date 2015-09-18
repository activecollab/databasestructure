<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\String;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class FieldDefinitionTest extends TestCase
{
    public function testScalarFieldsShouldBeAddedToModelByDefault()
    {
        $this->assertTrue((new String('is_important'))->getShouldBeAddedToModel());
    }

    /**
     * Test if scalar fields can be omitted from model
     */
    public function testScalarFieldsCanBeOmittedFromModel()
    {
        $this->assertFalse((new String('is_important'))->setShouldBeAddedToModel(false)->getShouldBeAddedToModel());
    }
}