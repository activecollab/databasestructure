<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Test\Fixtures\JsonField\JsonFieldStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @package ActiveCollab\DatabaseStructure\Test\ScalarFields
 */
class JsonFieldValueExtractionTest extends TestCase
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

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new JsonFieldStructure();

        if (!class_exists("{$this->namespace}\\StatsSnapshot", false)) {
            $this->structure->build();
        }

        $this->stats_snapshot_base_class_reflection = new ReflectionClass("{$this->namespace}\\Base\\StatsSnapshot");
        $this->stats_snapshot_class_reflection = new ReflectionClass("{$this->namespace}\\StatsSnapshot");
    }

    public function testGeneratedFieldsPropertyIsSet()
    {
        $generated_fields = $this->stats_snapshot_base_class_reflection->getDefaultProperties()['generated_fields'];

        $this->assertInternalType('array', $generated_fields);
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
}
