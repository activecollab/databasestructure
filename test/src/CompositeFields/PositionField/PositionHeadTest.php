<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields\PositionField;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\PositionHead\PositionHeadStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Purpose of this test is to see if files and tables are properly build from BlogStructure.
 */
class PositionHeadTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\PositionHead\\PositionHeadEntry';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var PositionHeadStructure
     */
    private $position_head_structure;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->pool = new Pool($this->connection, $this->createMock(LoggerInterface::class));
        $this->position_head_structure = new PositionHeadStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->position_head_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('position_head_entries')) {
            $this->connection->dropTable('position_head_entries');
        }

        $type_table_builder = new TypeTableBuilder($this->position_head_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->position_head_structure->getType('position_head_entries'));

        $this->assertTrue($this->connection->tableExists('position_head_entries'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));
    }

    /**
     * Tear down test environment.
     */
    public function tearDown(): void
    {
        if ($this->connection->tableExists('position_head_entries')) {
            $this->connection->dropTable('position_head_entries');
        }

        parent::tearDown();
    }

    /**
     * Test if position mode is head.
     */
    public function testPositionModeIsHead()
    {
        /** @var PositionInterface $entry */
        $entry = $this->pool->produce($this->type_class_name);

        $this->assertEquals(PositionInterface::POSITION_MODE_HEAD, $entry->getPositionMode());
    }

    /**
     * Test if position context is empty.
     */
    public function testPositionContextIsEmptyArray()
    {
        /** @var PositionInterface $entry */
        $entry = $this->pool->produce($this->type_class_name);

        $this->assertEquals([], $entry->getPositionContext());
    }

    /**
     * Test first record.
     */
    public function testFirstRecord()
    {
        /** @var PositionInterface $entry */
        $entry = $this->pool->produce($this->type_class_name);

        $this->assertInstanceOf($this->type_class_name, $entry);

        $this->assertEquals(1, $entry->getPosition());
    }

    /**
     * Test if all new records go to the begining of the list.
     */
    public function testNewRecordsGoToHead()
    {
        /** @var PositionInterface $entry1 */
        /* @var PositionInterface $entry2 */
        /* @var PositionInterface $entry3 */
        $entry1 = $this->pool->produce($this->type_class_name);
        $entry2 = $this->pool->produce($this->type_class_name);
        $entry3 = $this->pool->produce($this->type_class_name);

        $this->assertInstanceOf($this->type_class_name, $entry1);
        $this->assertInstanceOf($this->type_class_name, $entry2);
        $this->assertInstanceOf($this->type_class_name, $entry3);

        $this->assertEquals(1, $entry1->getPosition());
        $this->assertEquals(1, $entry2->getPosition());
        $this->assertEquals(1, $entry3->getPosition());

        $reloaded_entry_1 = $this->pool->reload($this->type_class_name, $entry1->getId());
        $reloaded_entry_2 = $this->pool->reload($this->type_class_name, $entry2->getId());
        $reloaded_entry_3 = $this->pool->reload($this->type_class_name, $entry3->getId());

        $this->assertEquals(3, $reloaded_entry_1->getPosition());
        $this->assertEquals(2, $reloaded_entry_2->getPosition());
        $this->assertEquals(1, $reloaded_entry_3->getPosition());
    }
}
