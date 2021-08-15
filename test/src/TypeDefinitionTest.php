<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Entity\Entity;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Test\Fixtures\EntityClassDescendent;
use ActiveCollab\DatabaseStructure\Test\Fixtures\EntityInterfaceDescendent;
use ActiveCollab\DatabaseStructure\Type;
use DateTime;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class TypeDefinitionTest extends TestCase
{
    /**
     * Test if types extend Object class of DatabaseObject package by default.
     */
    public function testTypeInheritsDatabaseObjectByDefault()
    {
        $this->assertEquals(Entity::class, (new Type('writers'))->getBaseClassExtends());
    }

    /**
     * Test if types can extend any class that descends from Entity class of DatabaseObject package.
     */
    public function testBaseClassCanExtendEntityClassDescendent()
    {
        $type = (new Type('writers'))->setBaseClassExtends(EntityClassDescendent::class);
        $this->assertEquals(EntityClassDescendent::class, $type->getBaseClassExtends());
    }

    public function testBaseClassCantExtendNonEntityClassDescendent()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Type('writers'))->setBaseClassExtends(DateTime::class);
    }

    /**
     * Test if types can extend any class that descends from EntityInterface class of DatabaseObject package.
     */
    public function testBaseInterfaceCanExtendEntityInterfaceDescendent()
    {
        $type = (new Type('writers'))->setBaseInterfaceExtends(EntityInterfaceDescendent::class);
        $this->assertEquals(EntityInterfaceDescendent::class, $type->getBaseInterfaceExtends());
    }

    public function testBaseInterfaceCantExtendNonEntityInterfaceDescendent()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Type('writers'))->setBaseInterfaceExtends(DateTime::class);
    }

    /**
     * Test if table name defaults to type name.
     */
    public function testTableNameDefaultsToTypeName()
    {
        $this->assertEquals('writers', (new Type('writers'))->getTableName());
    }

    /**
     * Test if table name can be changed.
     */
    public function testTableNameCanBeChanged()
    {
        $type = (new Type('writers'))->setTableName('awesome_writers');
        $this->assertEquals('awesome_writers', $type->getTableName());
    }

    /**
     * Check if expected dataset size is normal.
     */
    public function testExcepectedDatasetSizeDefaultsToNormal()
    {
        $this->assertEquals(FieldInterface::SIZE_NORMAL, (new Type('writers'))->getExpectedDatasetSize());
    }

    /**
     * Check if expected dataset size can be changed.
     */
    public function testExpectedDatasetSizeCanBeChanged()
    {
        $this->assertEquals(FieldInterface::SIZE_BIG, (new Type('writers'))->expectedDatasetSize(FieldInterface::SIZE_BIG)->getExpectedDatasetSize());
    }

    /**
     * Test if change to expected dataset size changes configuration of ID field.
     */
    public function testExpectedDatasetSizeChangeChangesIdField()
    {
        $type = new Type('writers');
        $this->assertEquals(FieldInterface::SIZE_NORMAL, $type->getIdField()->getSize());

        $type->expectedDatasetSize(FieldInterface::SIZE_BIG);

        $this->assertEquals(FieldInterface::SIZE_BIG, $type->getIdField()->getSize());
    }

    /**
     * Test default order by value.
     */
    public function testDefaultOrderBy()
    {
        $this->assertEquals(['id'], (new Type('writers'))->getOrderBy());
    }

    public function testOrderByNeedsToBeAnArray()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Type('writers'))->orderBy(false);
    }

    public function testOrderByCantBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Type('writers'))->orderBy([]);
    }

    /**
     * Test if order by can be changed.
     */
    public function testOrderByCanBeChanged()
    {
        $this->assertEquals(['!name', 'id'], (new Type('writers'))->orderBy(['!name', 'id'])->getOrderBy());
    }
}
