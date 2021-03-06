<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class FieldDefinitionTest extends TestCase
{
    /**
     * Test if scalar fields are added to the model by default.
     */
    public function testScalarFieldsShouldBeAddedToModelByDefault()
    {
        $this->assertTrue((new StringField('is_important'))->getShouldBeAddedToModel());
    }

    /**
     * Test if scalar fields can be omitted from model.
     */
    public function testScalarFieldsCanBeOmittedFromModel()
    {
        $this->assertFalse((new StringField('is_important'))->setShouldBeAddedToModel(false)->getShouldBeAddedToModel());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Default value can't NULL empty for required fields.
     */
    public function testDefaultValueCantBeNullForRequiredFields()
    {
        (new StringField('should_not_be_null', null))->required();
    }
}
