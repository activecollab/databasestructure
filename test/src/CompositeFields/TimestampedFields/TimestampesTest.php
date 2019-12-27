<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields\TimestampedFields;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface;
use ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface;
use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Timestamps\TimestampsStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DateValue\DateTimeValue;

class TimestampesTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Timestamps\\TimestampedEntry\\TimestampedEntry';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var TimestampsStructure
     */
    private $structure;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->pool = new Pool($this->connection);
        $this->structure = new TimestampsStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('timestamped_entries')) {
            $this->connection->dropTable('timestamped_entries');
        }

        $type_table_builder = new TypeTableBuilder($this->structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->structure->getType('timestamped_entries'));

        $this->assertTrue($this->connection->tableExists('timestamped_entries'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));

        $this->setNow(new DateTimeValue('2017-01-01 15:02:07'));
    }

    public function testBothTimestampsAreSetOnInsert()
    {
        /** @var CreatedAtInterface|UpdatedAtInterface $entry */
        $entry = $this->pool->produce($this->type_class_name, [
            'name' => 'Testing',
        ]);
        $this->assertInstanceOf($this->type_class_name, $entry);

        $this->assertNotEmpty($entry->getCreatedAt());
        $this->assertNotEmpty($entry->getUpdatedAt());
        $this->assertSame($entry->getCreatedAt()->getTimestamp(), $entry->getUpdatedAt()->getTimestamp());

        $this->assertSame('2017-01-01 15:02:07', $entry->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    public function testUpdatedAtIsRefreshedOnUpdate()
    {
        /** @var EntityInterface|CreatedAtInterface|UpdatedAtInterface $entry */
        $entry = $this->pool->produce($this->type_class_name, [
            'name' => 'Testing',
        ]);
        $this->assertInstanceOf($this->type_class_name, $entry);

        $this->assertSame('2017-01-01 15:02:07', $entry->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2017-01-01 15:02:07', $entry->getUpdatedAt()->format('Y-m-d H:i:s'));

        $this->setNow(new DateTimeValue('2017-02-17 15:22:18'));

        $entry
            ->setFieldValue('name', 'Updated Name')
            ->save();

        $this->assertSame('2017-01-01 15:02:07', $entry->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2017-02-17 15:22:18', $entry->getUpdatedAt()->format('Y-m-d H:i:s'));
    }
}
