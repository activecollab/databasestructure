<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields\JsonExtract;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\JsonField\JsonFieldStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\DateValue\DateValue;
use ActiveCollab\DateValue\DateValueInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

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

        if (!class_exists("{$this->namespace}\\StatsSnapshot\\StatsSnapshot", false)) {
            $this->structure->build();
        }

        $this->stats_snapshot_class_name = "{$this->namespace}\\StatsSnapshot\\StatsSnapshot";

        $this->stats_snapshot_base_class_reflection = new ReflectionClass("{$this->namespace}\\StatsSnapshot\\Base\\BaseStatsSnapshot");
        $this->stats_snapshot_class_reflection = new ReflectionClass($this->stats_snapshot_class_name);

        $type_table_build = new TypeTableBuilder($this->structure);
        $type_table_build->setConnection($this->connection);

        $stats_snapshots_type = $this->structure->getType('stats_snapshots');
        $this->assertInstanceOf(TypeInterface::class, $stats_snapshots_type);

        $create_table_statement = $type_table_build->prepareCreateTableStatement($stats_snapshots_type);

        $this->connection->execute($create_table_statement);

        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);

        $this->pool = new Pool($this->connection, $logger);
        $this->pool->registerType($this->stats_snapshot_class_name);
    }

    public function testDefaultValues()
    {
        $stats_snapshot = $this->pool->produce($this->stats_snapshot_class_name, [
            'day' => new DateValue('2016-11-28'),
            'stats' => null,
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertNull($stats_snapshot->getPlanName());
        $this->assertNull($stats_snapshot->getNumberOfActiveUsers());
        $this->assertNull($stats_snapshot->isUsedOnDay());
        $this->assertSame(0.0, $stats_snapshot->getExecutionTime());

        /** @var DateValueInterface $important_date */
        $important_date = $stats_snapshot->getImportantDate1();
        $this->assertInstanceOf(DateValueInterface::class, $important_date);
        $this->assertSame('2013-10-02', $important_date->format('Y-m-d'));

        $important_date_time = $stats_snapshot->getImportantDate2WithTime();
        $this->assertInstanceOf(DateTimeValueInterface::class, $important_date_time);
        $this->assertSame('2016-05-09 09:11:00', $important_date_time->format('Y-m-d h:i:s'));
    }

    /**
     * @param mixed $current_test
     * @dataProvider validTruesProvider
     */
    public function testValueThatCastsToTrue($current_test)
    {
        $stats_snapshot = $this->pool->produce($this->stats_snapshot_class_name, [
            'day' => new DateValue('2016-11-28'),
            'stats' => [
                'is_used_on_day' => $current_test,
            ],
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertTrue($stats_snapshot->isUsedOnDay());
    }

    /**
     * @return array
     */
    public function validTruesProvider()
    {
        return [
            [true],
            ['true'],
            [-12],
            [12],
        ];
    }

    /**
     * @param mixed $current_test
     * @dataProvider validFalsesProvider
     */
    public function testValueThatCastsToFalse($current_test)
    {
        $stats_snapshot = $this->pool->produce($this->stats_snapshot_class_name, [
            'day' => new DateValue('2016-11-28'),
            'stats' => [
                'is_used_on_day' => $current_test,
            ],
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertFalse($stats_snapshot->isUsedOnDay());
    }

    /**
     * @return array
     */
    public function validFalsesProvider()
    {
        return [
            [false],
            ['false'],
            [0],
            [12.34],
            ['something other'],
            ['what not'],
        ];
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
                'is_used_on_day' => true,
            ],
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertSame('MEGA', $stats_snapshot->getPlanName());
        $this->assertSame(123, $stats_snapshot->getNumberOfActiveUsers());
        $this->assertTrue($stats_snapshot->isUsedOnDay());
    }

    public function testUpdate()
    {
        $stats_snapshot = $this->pool->produce($this->stats_snapshot_class_name, [
            'day' => new DateValue('2016-11-28'),
            'stats' => [
                'plan_name' => 'MEGA',
                'users' => [
                    'num_active' => 123,
                ],
                'is_used_on_day' => true,
            ],
        ]);
        $this->assertInstanceOf($this->stats_snapshot_class_name, $stats_snapshot);

        $this->assertSame('MEGA', $stats_snapshot->getPlanName());
        $this->assertSame(123, $stats_snapshot->getNumberOfActiveUsers());
        $this->assertTrue($stats_snapshot->isUsedOnDay());

        $stats_snapshot->setStats([
            'plan_name' => 'Lerge',
            'users' => [
                'num_active' => 321,
            ],
            'is_used_on_day' => false,
        ])->save();

        $this->assertSame('Lerge', $stats_snapshot->getPlanName());
        $this->assertSame(321, $stats_snapshot->getNumberOfActiveUsers());
        $this->assertFalse($stats_snapshot->isUsedOnDay());
    }
}
