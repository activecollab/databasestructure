<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Type;

class PolymorphTypeDbTest extends DbTestCase
{
    /**
     * Test if types are not polymorph by default.
     */
    public function testTypesAreNotPolymorphByDefault()
    {
        $this->assertFalse((new Type('writers'))->getPolymorph());
    }

    /**
     * Test if we can make a type polymorph.
     */
    public function testTypesCanBeSetAsPolymorph()
    {
        $this->assertTrue((new Type('writers'))->polymorph()->getPolymorph());
    }

    /**
     * Check if polymorphism adds type field.
     */
    public function testPolymorphismAddsTypeField()
    {
        $writers = (new Type('writers'))->polymorph();

        $fields = $writers->getAllFields();

        $this->assertIsArray($fields);

        $type_field_found = false;
        $type_field_is_required = false;
        $type_field_default_value = null;

        /** @var ScalarField $field */
        foreach ($fields as $field) {
            if ($field->getName() == 'type') {
                $type_field_found = true;
                $type_field_is_required = $field->isRequired();
                $type_field_default_value = $field->getDefaultValue();
            }
        }

        $this->assertTrue($type_field_found);
        $this->assertTrue($type_field_is_required);
        $this->assertSame('', $type_field_default_value);
    }

    /**
     * Check if polymorphism adds type index.
     */
    public function testPolymorphismAddsTypeIndex()
    {
        $writers = (new Type('writers'))->polymorph();

        $indexes = $writers->getAllIndexes();

        $this->assertIsArray($indexes);

        $type_index_found = false;

        foreach ($indexes as $index) {
            if ($index->getName() == 'type') {
                $type_index_found = true;
            }
        }

        $this->assertTrue($type_index_found);
    }
}
