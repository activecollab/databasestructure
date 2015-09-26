<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Field\Scalar\Field as ScalarField;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class PolymorphTypeTest extends TestCase
{
    /**
     * Test if types are not polymorph by default
     */
    public function testTypesAreNotPolymorphByDefault()
    {
        $this->assertFalse((new Type('writers'))->getPolymorph());
    }

    /**
     * Test if we can make a type polymorph
     */
    public function testTypesCanBeSetAsPolymorph()
    {
        $this->assertTrue((new Type('writers'))->polymorph()->getPolymorph());
    }

    /**
     * Check if polymorphism adds type field
     */
    public function testPolymorphismAddsTypeField()
    {
        $writers = (new Type('writers'))->polymorph();

        $fields = $writers->getAllFields();

        $this->assertInternalType('array', $fields);

        $type_field_found = false;
        $type_field_default_value = null;

        /** @var ScalarField $field */
        foreach ($fields as $field) {
            if ($field->getName() == 'type') {
                $type_field_found = true;
                $type_field_default_value = $field->getDefaultValue();
            }
        }

        $this->assertTrue($type_field_found);
        $this->assertSame('', $type_field_default_value);
    }

    /**
     * Check if polymorphism adds type index
     */
    public function testPolymorphismAddsTypeIndex()
    {
        $writers = (new Type('writers'))->polymorph();

        $indexes = $writers->getAllIndexes();

        $this->assertInternalType('array', $indexes);

        $type_index_found = false;

        foreach ($indexes as $index) {
            if ($index->getName() == 'type') {
                $type_index_found = true;
            }
        }

        $this->assertTrue($type_index_found);
    }
}