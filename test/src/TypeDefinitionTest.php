<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseObject\Object;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Test\Fixtures\ObjectClassDescendent;
use DateTime;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class TypeDefinitionTest extends TestCase
{
    /**
     * Test if types extend Object class of DatabaseObject package by default
     */
    public function testTypeInheritsDatabaseObjectByDefault()
    {
        $this->assertEquals(Object::class, (new Type('writers'))->getBaseClassExtends());
    }

    /**
     * Test if types can extend any class that descends from Object class of DatabaseObject package
     */
    public function testBaseClassCanExtendObjectClassDescendent()
    {
        $type = (new Type('writers'))->setBaseClassExtends(ObjectClassDescendent::class);
        $this->assertEquals(ObjectClassDescendent::class, $type->getBaseClassExtends());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBaseClassCantExtendNonObjectClassDescendent()
    {
        (new Type('writers'))->setBaseClassExtends(DateTime::class);
    }

    /**
     * Test if table name defaults to type name
     */
    public function testTableNameDefaultsToTypeName()
    {
        $this->assertEquals('writers', (new Type('writers'))->getTableName());
    }

    /**
     * Test if table name can be changed
     */
    public function testTableNameCanBeChanged()
    {
        $type = (new Type('writers'))->setTableName('awesome_writers');
        $this->assertEquals('awesome_writers', $type->getTableName());
    }

    public function testExcepectedDatasetSizeDefaultsToNormal()
    {
        $this->assertEquals(FieldInterface::SIZE_NORMAL, (new Type('writers'))->getExpectedDatasetSize());
    }

    public function testExpectedDatasetSizeCanBeChanged()
    {
        $this->assertEquals(FieldInterface::SIZE_BIG, (new Type('writers'))->setExpectedDatasetSize(FieldInterface::SIZE_BIG)->getExpectedDatasetSize());
    }
}