<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseObject\Object;
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

    public function testTableNameDefaultsToTypeName()
    {
        $this->assertEquals('writers', (new Type('writers'))->getTableName());
    }

    public function testTableNameCanBeChanged()
    {
        $type = (new Type('writers'))->setTableName('awesome_writers');
        $this->assertEquals('awesome_writers', $type->getTableName());
    }
}