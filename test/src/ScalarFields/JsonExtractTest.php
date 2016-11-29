<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\JsonField\JsonFieldStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DateValue\DateValue;
use ReflectionClass;

/**
 * @package ActiveCollab\DatabaseStructure\Test\ScalarFields
 */
class JsonExtractTest extends TestCase
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var JsonFieldStructure
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\JsonField';

    /**
     * @var string
     */
    private $stats_snapshot_class_name;

    /**
     * @var ReflectionClass
     */
    private $stats_snapshot_base_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $stats_snapshot_class_reflection;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new JsonFieldStructure();

        if (!class_exists("{$this->namespace}\\StatsSnapshot", false)) {
            $this->structure->build();
        }

        $this->stats_snapshot_class_name = "{$this->namespace}\\StatsSnapshot";

        $this->stats_snapshot_base_class_reflection = new ReflectionClass("{$this->namespace}\\Base\\StatsSnapshot");
        $this->stats_snapshot_class_reflection = new ReflectionClass($this->stats_snapshot_class_name);

        $type_table_build = new TypeTableBuilder($this->structure);
        $type_table_build->setConnection($this->connection);

        $stats_snapshots_type = $this->structure->getType('stats_snapshots');
        $this->assertInstanceOf(TypeInterface::class, $stats_snapshots_type);

        $create_table_statement = $type_table_build->prepareCreateTableStatement($stats_snapshots_type);

        $this->connection->execute($create_table_statement);

        $this->pool = new Pool($this->connection);
        $this->pool->registerType($this->stats_snapshot_class_name);
    }

    public function testEmptyStatsDefaultToNull()
    {
        $stats_snapshot = $this->pool->produce($this->stats_snapshot_class_name, [
            'day' => new DateValue('2016-11-28'),
            'stats' => null,
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertNull($stats_snapshot->getPlanName());
        $this->assertNull($stats_snapshot->getNumberOfActiveUsers());
        $this->assertNull($stats_snapshot->isUsedOnDay());
    }

    public function testExtractionAndCasting()
    {
        $stats_snapshot = $this->pool->produce($this->stats_snapshot_class_name, [
            'day' => new DateValue('2016-11-28'),
            'stats' => [
                'plan_name' => 'MEGA',
                'users' => [
                    'num_active' => 123,
                ],
                'is_used_on_day' => 1,
            ],
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertSame('MEGA', $stats_snapshot->getPlanName());
        $this->assertSame(123, $stats_snapshot->getNumberOfActiveUsers());
        $this->assertTrue($stats_snapshot->isUsedOnDay());
    }
}