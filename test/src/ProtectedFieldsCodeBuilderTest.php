<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface\Implementation as ProtectedFieldsInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Test\Fixtures\ProtectedFields\ProtectedFieldsStructure;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class ProtectedFieldsCodeBuilderTest extends TestCase
{
    /**
     * @var ProtectedFieldsStructure
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\ProtectedFields';

    /**
     * @var ReflectionClass
     */
    private $no_protected_fields_base_class_reflection, $no_protected_fields_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $has_protected_fields_base_class_reflection, $has_protected_fields_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $multi_protected_fields_base_class_reflection, $multi_protected_fields_class_reflection;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new ProtectedFieldsStructure();

        if (!class_exists("{$this->namespace}\\NoProtectedField\\NoProtectedField", false)) {
            $this->structure->build();
        }

        $this->no_protected_fields_base_class_reflection = new ReflectionClass("{$this->namespace}\\NoProtectedField\\Base\\BaseNoProtectedField");
        $this->no_protected_fields_class_reflection = new ReflectionClass("{$this->namespace}\\NoProtectedField\\NoProtectedField");

        $this->has_protected_fields_base_class_reflection = new ReflectionClass("{$this->namespace}\\HasProtectedField\\Base\\BaseHasProtectedField");
        $this->has_protected_fields_class_reflection = new ReflectionClass("{$this->namespace}\\HasProtectedField\\HasProtectedField");

        $this->multi_protected_fields_base_class_reflection = new ReflectionClass("{$this->namespace}\\MultiProtectedField\\Base\\BaseMultiProtectedField");
        $this->multi_protected_fields_class_reflection = new ReflectionClass("{$this->namespace}\\MultiProtectedField\\MultiProtectedField");
    }

    /**
     * Test structure settings.
     */
    public function testStructureSettings()
    {
        $this->assertEquals([], $this->structure->getType('no_protected_fields')->getProtectedFields());
        $this->assertEquals(['field_1', 'field_2'], $this->structure->getType('has_protected_fields')->getProtectedFields());
        $this->assertEquals(['field_1', 'field_2', 'field_3'], $this->structure->getType('multi_protected_fields')->getProtectedFields());
    }

    /**
     * Test if base classes implement the interface.
     */
    public function testBaseClassesImplementsProtectedFieldsInterface()
    {
        $this->assertFalse($this->no_protected_fields_base_class_reflection->implementsInterface(ProtectedFieldsInterface::class));
        $this->assertTrue($this->has_protected_fields_base_class_reflection->implementsInterface(ProtectedFieldsInterface::class));
        $this->assertTrue($this->multi_protected_fields_base_class_reflection->implementsInterface(ProtectedFieldsInterface::class));
    }

    /**
     * Test if base classes use protected fields trait.
     */
    public function testBaseClassesImplementProtectedFieldsTrait()
    {
        $this->assertNotContains(
            ProtectedFieldsInterfaceImplementation::class,
            $this->no_protected_fields_base_class_reflection->getTraitNames()
        );
        $this->assertContains(
            ProtectedFieldsInterfaceImplementation::class,
            $this->has_protected_fields_base_class_reflection->getTraitNames()
        );
        $this->assertContains(
            ProtectedFieldsInterfaceImplementation::class,
            $this->multi_protected_fields_base_class_reflection->getTraitNames()
        );
    }

    /**
     * Test if instances return proper field lists.
     */
    public function testInstancesReturnListsOfProtectedFields()
    {
        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);

        $pool = new Pool($this->connection, $logger);

        $this->assertEquals(
            ['field_1', 'field_2'],
            $this->has_protected_fields_class_reflection->newInstance($this->connection, $pool, $logger)->getProtectedFields()
        );
        $this->assertEquals(
            ['field_1', 'field_2', 'field_3'],
            $this->multi_protected_fields_class_reflection->newInstance($this->connection, $pool, $logger)->getProtectedFields()
        );
    }
}
