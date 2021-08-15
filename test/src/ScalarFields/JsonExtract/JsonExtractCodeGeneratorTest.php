<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields\JsonExtract;

use ActiveCollab\DatabaseStructure\Builder\Database\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\JsonField\JsonFieldStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ReflectionClass;
use ReflectionMethod;

class JsonExtractCodeGeneratorTest extends TestCase
{
    /**
     * @var JsonFieldStructure
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\JsonField';

    /**
     * @var ReflectionClass
     */
    private $stats_snapshot_base_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $stats_snapshot_class_reflection;

    public function setUp(): void
    {
        parent::setUp();

        $this->structure = new JsonFieldStructure();

        if (!class_exists("{$this->namespace}\\StatsSnapshot\\StatsSnapshot", false)) {
            $this->structure->build();
        }

        $this->stats_snapshot_base_class_reflection = new ReflectionClass("{$this->namespace}\\StatsSnapshot\\Base\\BaseStatsSnapshot");
        $this->stats_snapshot_class_reflection = new ReflectionClass("{$this->namespace}\\StatsSnapshot\\StatsSnapshot");
    }

    public function tearDown(): void
    {
        $this->connection->dropTable('stats_snapshots');

        parent::tearDown();
    }

    public function testGeneratedFieldsPropertyIsSet()
    {
        $generated_fields = $this->stats_snapshot_base_class_reflection->getDefaultProperties()['generated_fields'];

        $this->assertIsArray($generated_fields);
        $this->assertContains('number_of_active_users', $generated_fields);
    }

    public function testAccessorsAreAdded()
    {
        $this->assertInstanceOf(ReflectionMethod::class, $this->stats_snapshot_base_class_reflection->getMethod('getPlanName'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->stats_snapshot_base_class_reflection->getMethod('getNumberOfActiveUsers'));

        $this->assertInstanceOf(ReflectionMethod::class, $this->stats_snapshot_base_class_reflection->getMethod('getIsUsedOnDay'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->stats_snapshot_base_class_reflection->getMethod('isUsedOnDay'));
    }

    public function testConfigureMethodIsOverriden()
    {
        $configure_method = $this->stats_snapshot_base_class_reflection->getMethod('configure');

        $this->assertInstanceOf(ReflectionMethod::class, $configure_method);
        $this->assertEquals($this->stats_snapshot_base_class_reflection->getName(), $configure_method->getDeclaringClass()->getName());
    }

    public function testCreateTable()
    {
        $type_table_build = new TypeTableBuilder($this->structure);
        $type_table_build->setConnection($this->connection);

        $stats_snapshots_type = $this->structure->getType('stats_snapshots');
        $this->assertInstanceOf(TypeInterface::class, $stats_snapshots_type);

        $create_table_statement = $type_table_build->prepareCreateTableStatement($stats_snapshots_type);

        $this->assertStringContainsString("`plan_name` VARCHAR(191) AS (IF(JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.plan_name')) IS NULL, NULL, JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.plan_name')))) STORED", $create_table_statement);
        $this->assertStringContainsString("`number_of_active_users` INT AS (IF(JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.users.num_active')) IS NULL, NULL, CAST(JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.users.num_active')) AS SIGNED INTEGER))) STORED", $create_table_statement);
        $this->assertStringContainsString("`is_used_on_day` TINYINT(1) UNSIGNED AS (IF(JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.is_used_on_day')) IS NULL, NULL, IF(JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.is_used_on_day')) = 'true' OR (JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.is_used_on_day')) REGEXP '^-?[0-9]+$' AND CAST(JSON_UNQUOTE(JSON_EXTRACT(`stats`, '$.is_used_on_day')) AS SIGNED) != 0), 1, 0))) VIRTUAL", $create_table_statement);
        $this->assertStringContainsString('INDEX `plan_name` (`plan_name`)', $create_table_statement);
        $this->assertStringNotContainsString('INDEX `number_of_active_users` (`number_of_active_users`)', $create_table_statement);
        $this->assertStringNotContainsString('INDEX `number_of_active_users` (`number_of_active_users`)', $create_table_statement);
    }
}
