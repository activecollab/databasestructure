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
use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\PositionTail\PositionTailStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class PositionTailTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\PositionTail\\PositionTailEntry\\PositionTailEntry';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var PositionTailStructure
     */
    private $position_tail_structure;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);

        $this->pool = new Pool($this->connection, $logger);
        $this->position_tail_structure = new PositionTailStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->position_tail_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('position_tail_entries')) {
            $this->connection->dropTable('position_tail_entries');
        }

        $type_table_builder = new TypeTableBuilder($this->position_tail_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->position_tail_structure->getType('position_tail_entries'));

        $this->assertTrue($this->connection->tableExists('position_tail_entries'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));
    }

    /**
     * Tear down test environment.
     */
    public function tearDown(): void
    {
        if ($this->connection->tableExists('position_tail_entries')) {
            $this->connection->dropTable('position_tail_entries');
        }

        parent::tearDown();
    }

    /**
     * Test if position mode is tail.
     */
    public function testPositionModeIsTail()
    {
        /** @var PositionInterface $entry */
        $entry = $this->pool->produce($this->type_class_name);

        $this->assertEquals(PositionInterface::POSITION_MODE_TAIL, $entry->getPositionMode());
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
    public function testNewRecordsGoToTail()
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
        $this->assertEquals(2, $entry2->getPosition());
        $this->assertEquals(3, $entry3->getPosition());
    }
}
