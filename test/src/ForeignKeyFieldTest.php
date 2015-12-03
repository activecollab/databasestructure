<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Composite\ForeignKeyField;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class ForeignKeyFieldTest extends TestCase
{
    /**
     * Test if FK is required by default
     */
    public function testForeignKeyIsRequiredByDefault()
    {
        $this->assertTrue((new ForeignKeyField('application_id'))->isRequired());
    }

    /**
     * Test if FK can be made optional
     */
    public function testForeignKeyCanBeMadeOptional()
    {
        $this->assertFalse((new ForeignKeyField('application_id'))->required(false)->isRequired());
    }

    /**
     * Test if FK produces a valid integer field
     */
    public function testForeignKeyProducesIntegerField()
    {
        $fk = new ForeignKeyField('application_id');

        $fields = $fk->getFields();

        $this->assertInternalType('array', $fields);
        $this->assertCount(1, $fields);

        /** @var IntegerField $fk_field */
        $fk_field = $fields[0];

        $this->assertInstanceOf(IntegerField::class, $fk_field);
        $this->assertEquals('application_id', $fk_field->getName());
        $this->assertTrue($fk_field->isRequired());
        $this->assertTrue($fk_field->getUnsigned());
        $this->assertEquals($fk->getSize(), $fk_field->getSize());
    }

    /**
     * Test if required flag change is passed on to the field
     */
    public function testForeignKeyPassesRequiredToField()
    {
        /** @var IntegerField $fk_field */
        $fk_field = (new ForeignKeyField('application_id'))->required(false)->getFields()[0];

        $this->assertInstanceOf(IntegerField::class, $fk_field);
        $this->assertFalse($fk_field->isRequired());
    }

    /**
     * Test if size change is passed on to the field
     */
    public function testForeignKeyPassesSizeToField()
    {
        /** @var IntegerField $fk_field */
        $fk_field = (new ForeignKeyField('application_id'))->size(FieldInterface::SIZE_BIG)->getFields()[0];

        $this->assertInstanceOf(IntegerField::class, $fk_field);
        $this->assertEquals(FieldInterface::SIZE_BIG, $fk_field->getSize());
    }
}
