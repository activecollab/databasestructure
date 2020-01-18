<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields\PositionField;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\PositionContext\PositionContextStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use Psr\Log\LoggerInterface;

class PositionContextTest extends TestCase
{
    /**
     * @var string
     */
    private $head_type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\PositionContext\\PositionContextHeadEntry\\PositionContextHeadEntry';

    /**
     * @var string
     */
    private $tail_type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\PositionContext\\PositionContextTailEntry\\PositionContextTailEntry';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var PositionContextStructure
     */
    private $position_context_structure;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);

        $this->pool = new Pool($this->connection, $logger);
        $this->position_context_structure = new PositionContextStructure();

        if (!class_exists($this->head_type_class_name, false) && !class_exists($this->tail_type_class_name, false)) {
            $this->position_context_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('position_context_head_entries')) {
            $this->connection->dropTable('position_context_head_entries');
        }

        if ($this->connection->tableExists('position_context_tail_entries')) {
            $this->connection->dropTable('position_context_tail_entries');
        }

        $type_table_builder = new TypeTableBuilder($this->position_context_structure);
        $type_table_builder->setConnection($this->connection);

        $type_table_builder->buildType($this->position_context_structure->getType('position_context_head_entries'));
        $type_table_builder->buildType($this->position_context_structure->getType('position_context_tail_entries'));

        $this->assertTrue($this->connection->tableExists('position_context_head_entries'));
        $this->assertTrue($this->connection->tableExists('position_context_tail_entries'));

        $this->pool->registerType($this->head_type_class_name);
        $this->pool->registerType($this->tail_type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->head_type_class_name));
        $this->assertTrue($this->pool->isTypeRegistered($this->tail_type_class_name));
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        if ($this->connection->tableExists('position_context_head_entries')) {
            $this->connection->dropTable('position_context_head_entries');
        }

        if ($this->connection->tableExists('position_context_tail_entries')) {
            $this->connection->dropTable('position_context_tail_entries');
        }

        parent::tearDown();
    }

    /**
     * Test if position modes are properly set for registered type.
     */
    public function testPositionModes()
    {
        /** @var PositionInterface $head_entry */
        $head_entry = $this->pool->produce($this->head_type_class_name);

        $this->assertEquals(PositionInterface::POSITION_MODE_HEAD, $head_entry->getPositionMode());

        /** @var PositionInterface $tail_entry */
        $tail_entry = $this->pool->produce($this->tail_type_class_name);

        $this->assertEquals(PositionInterface::POSITION_MODE_TAIL, $tail_entry->getPositionMode());
    }

    /**
     * Test if position context is empty.
     */
    public function testPositionContexts()
    {
        /** @var PositionInterface $head_entry */
        $head_entry = $this->pool->produce($this->head_type_class_name);

        $this->assertEquals(['application_id', 'shard_id'], $head_entry->getPositionContext());

        /** @var PositionInterface $tail_entry */
        $tail_entry = $this->pool->produce($this->head_type_class_name);

        $this->assertEquals(['application_id', 'shard_id'], $tail_entry->getPositionContext());
    }

    /**
     * Test first record.
     */
    public function testTail()
    {
        /** @var ObjectInterface|PositionInterface $entry1 */
        /* @var ObjectInterface|PositionInterface $entry2 */
        /* @var ObjectInterface|PositionInterface $entry3 */
        /* @var ObjectInterface|PositionInterface $entry4 */
        $entry1 = $this->pool->produce($this->tail_type_class_name, ['application_id' => 1, 'shard_id' => 1]);
        $entry2 = $this->pool->produce($this->tail_type_class_name, ['application_id' => 1, 'shard_id' => 1]);
        $entry3 = $this->pool->produce($this->tail_type_class_name, ['application_id' => 1, 'shard_id' => 1]);
        $entry4 = $this->pool->produce($this->tail_type_class_name, ['application_id' => 1, 'shard_id' => 2]);

        $this->assertInstanceOf($this->tail_type_class_name, $entry1);
        $this->assertInstanceOf($this->tail_type_class_name, $entry2);
        $this->assertInstanceOf($this->tail_type_class_name, $entry3);
        $this->assertInstanceOf($this->tail_type_class_name, $entry4);

        /** @var ObjectInterface $v */
        foreach ([$entry1, $entry2, $entry3] as $v) {
            $this->assertEquals(1, $v->getFieldValue('application_id'));
            $this->assertEquals(1, $v->getFieldValue('shard_id'));
        }

        $this->assertEquals(1, $entry4->getFieldValue('application_id'));
        $this->assertEquals(2, $entry4->getFieldValue('shard_id'));

        $this->assertEquals(1, $entry1->getPosition());
        $this->assertEquals(2, $entry2->getPosition());
        $this->assertEquals(3, $entry3->getPosition());
        $this->assertEquals(1, $entry4->getPosition());
    }

    /**
     * Test if all new records go to the begining of the list.
     */
    public function testHead()
    {
        /** @var ObjectInterface|PositionInterface $entry1 */
        /* @var ObjectInterface|PositionInterface $entry2 */
        /* @var ObjectInterface|PositionInterface $entry3 */
        /* @var ObjectInterface|PositionInterface $entry4 */
        $entry1 = $this->pool->produce($this->head_type_class_name, ['application_id' => 1, 'shard_id' => 1]);
        $entry2 = $this->pool->produce($this->head_type_class_name, ['application_id' => 1, 'shard_id' => 1]);
        $entry3 = $this->pool->produce($this->head_type_class_name, ['application_id' => 1, 'shard_id' => 1]);
        $entry4 = $this->pool->produce($this->head_type_class_name, ['application_id' => 1, 'shard_id' => 2]);

        $this->assertInstanceOf($this->head_type_class_name, $entry1);
        $this->assertInstanceOf($this->head_type_class_name, $entry2);
        $this->assertInstanceOf($this->head_type_class_name, $entry3);
        $this->assertInstanceOf($this->head_type_class_name, $entry4);

        /** @var ObjectInterface $v */
        foreach ([$entry1, $entry2, $entry3] as $v) {
            $this->assertEquals(1, $v->getFieldValue('application_id'));
            $this->assertEquals(1, $v->getFieldValue('shard_id'));
        }

        $this->assertEquals(1, $entry4->getFieldValue('application_id'));
        $this->assertEquals(2, $entry4->getFieldValue('shard_id'));

        $this->assertEquals(1, $entry1->getPosition());
        $this->assertEquals(1, $entry2->getPosition());
        $this->assertEquals(1, $entry3->getPosition());
        $this->assertEquals(1, $entry4->getPosition());

        $reloaded_entry_1 = $this->pool->reload($this->head_type_class_name, $entry1->getId());
        $reloaded_entry_2 = $this->pool->reload($this->head_type_class_name, $entry2->getId());
        $reloaded_entry_3 = $this->pool->reload($this->head_type_class_name, $entry3->getId());
        $reloaded_entry_4 = $this->pool->reload($this->head_type_class_name, $entry4->getId());

        $this->assertEquals(3, $reloaded_entry_1->getPosition());
        $this->assertEquals(2, $reloaded_entry_2->getPosition());
        $this->assertEquals(1, $reloaded_entry_3->getPosition());
        $this->assertEquals(1, $reloaded_entry_4->getPosition());
    }
}
