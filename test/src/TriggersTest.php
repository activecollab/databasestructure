<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseConnection\Result\Result;
use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Builder\Database\TriggersBuilder;
use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Triggers\TriggersStructure;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class TriggersTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Triggers\\Trigger';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var TriggersStructure
     */
    private $triggers_structure;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->pool = new Pool($this->connection);
        $this->triggers_structure = new TriggersStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->triggers_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('triggers')) {
            $this->connection->dropTable('triggers');
        }

        $type_table_builder = new TypeTableBuilder($this->triggers_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->triggers_structure->getType('triggers'));

        $type_triggers_builder = new TriggersBuilder($this->triggers_structure);
        $type_triggers_builder->setConnection($this->connection);
        $type_triggers_builder->buildType($this->triggers_structure->getType('triggers'));

        $this->assertTrue($this->connection->tableExists('triggers'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        if ($triggers = $this->connection->execute('SHOW TRIGGERS')) {
            foreach ($triggers as $trigger) {
                $this->connection->execute('DROP TRIGGER ' . $this->connection->escapeFieldName($trigger['Trigger']));
            }
        }

        if ($this->connection->tableExists('triggers')) {
            $this->connection->dropTable('triggers');
        }

        parent::tearDown();
    }

    /**
     * Test if triggers are created.
     */
    public function testTriggersAreCreated()
    {
        $triggers = $this->connection->execute('SHOW TRIGGERS');

        $this->assertInstanceOf(Result::class, $triggers);
        $this->assertCount(2, $triggers);
    }

    /**
     * Test before insert trigger.
     */
    public function testBeforeInsertTrigger()
    {
        $entry = $this->pool->produce($this->type_class_name, ['num' => 3]);

        $this->assertInstanceOf($this->type_class_name, $entry);
        $this->assertEquals(3, $entry->getNum());

        $reloaded_entry = $this->pool->reload($this->type_class_name, $entry->getId());
        $this->assertEquals(5, $reloaded_entry->getNum());
    }

    /**
     * Test before update trigger.
     */
    public function testBeforeUpdateTrigger()
    {
        $entry = $this->pool->produce($this->type_class_name, ['num' => 3]);

        $this->assertInstanceOf($this->type_class_name, $entry);
        $this->assertEquals(3, $entry->getNum());

        $reloaded_entry = $this->pool->reload($this->type_class_name, $entry->getId());
        $this->assertEquals(5, $reloaded_entry->getNum());

        $reloaded_entry->setNum(12)->save();
        $this->assertEquals(12, $reloaded_entry->getNum());

        $reloaded_entry_2 = $this->pool->reload($this->type_class_name, $entry->getId());
        $this->assertEquals(15, $reloaded_entry_2->getNum());
    }
}
