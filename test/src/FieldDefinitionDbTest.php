<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use LogicException;

class FieldDefinitionDbTest extends DbTestCase
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

    public function testDefaultValueCantBeNullForRequiredFields()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Default value can't NULL empty for required fields.");

        (new StringField('should_not_be_null', null))->required();
    }
}
