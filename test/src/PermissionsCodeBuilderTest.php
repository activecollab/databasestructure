<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface\PermissiveImplementation;
use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface\RestrictiveImplementation;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Permissions\PermissionsStructure;
use ReflectionClass;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class PermissionsCodeBuilderTest extends TestCase
{
    /**
     * @var PermissionsStructure
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\Permissions';

    /**
     * @var ReflectionClass
     */
    private $element_base_class_reflection, $element_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $restrictive_element_base_class_reflection, $restrictive_element_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $reverted_element_base_class_reflection, $reverted_element_class_reflection;

    /**
     * @var ReflectionClass
     */
    private $changed_element_base_class_reflection, $changed_element_class_reflection;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new PermissionsStructure();

        if (!class_exists("{$this->namespace}\\Element", false)) {
            $this->structure->build();
        }

        $this->element_base_class_reflection = new ReflectionClass("{$this->namespace}\\Base\\Element");
        $this->element_class_reflection = new ReflectionClass("{$this->namespace}\\Element");

        $this->restrictive_element_base_class_reflection = new ReflectionClass("{$this->namespace}\\Base\\RestrictiveElement");
        $this->restrictive_element_class_reflection = new ReflectionClass("{$this->namespace}\\RestrictiveElement");

        $this->reverted_element_base_class_reflection = new ReflectionClass("{$this->namespace}\\Base\\RevertedElement");
        $this->reverted_element_class_reflection = new ReflectionClass("{$this->namespace}\\RevertedElement");

        $this->changed_element_base_class_reflection = new ReflectionClass("{$this->namespace}\\Base\\ChangedElement");
        $this->changed_element_class_reflection = new ReflectionClass("{$this->namespace}\\ChangedElement");
    }

    /**
     * Test structure settings.
     */
    public function testStructureSettings()
    {
        $this->assertTrue($this->structure->getType('elements')->getPermissions());
        $this->assertTrue($this->structure->getType('elements')->getPermissionsArePermissive());

        $this->assertTrue($this->structure->getType('restrictive_elements')->getPermissions());
        $this->assertFalse($this->structure->getType('restrictive_elements')->getPermissionsArePermissive());

        $this->assertFalse($this->structure->getType('reverted_elements')->getPermissions());
        $this->assertTrue($this->structure->getType('reverted_elements')->getPermissionsArePermissive());

        $this->assertTrue($this->structure->getType('changed_elements')->getPermissions());
        $this->assertFalse($this->structure->getType('changed_elements')->getPermissionsArePermissive());
    }

    /**
     * Test if base classes implement the interface.
     */
    public function testBaseClassesImplementsPermissionsInterface()
    {
        $this->assertTrue($this->element_base_class_reflection->implementsInterface(PermissionsInterface::class));
        $this->assertTrue($this->restrictive_element_base_class_reflection->implementsInterface(PermissionsInterface::class));
        $this->assertFalse($this->reverted_element_base_class_reflection->implementsInterface(PermissionsInterface::class));
    }

    /**
     * Test if permissive trait is added to permissive class.
     */
    public function testPermissiveClassImplementsPermissiveTrait()
    {
        $this->assertContains(PermissiveImplementation::class, $this->element_base_class_reflection->getTraitNames());
        $this->assertNotContains(RestrictiveImplementation::class, $this->element_base_class_reflection->getTraitNames());
    }

    /**
     * Test if restrictive trait is added to restrictive class.
     */
    public function testRestrictiveClassImplementsRestrictiveTrait()
    {
        $this->assertNotContains(PermissiveImplementation::class, $this->restrictive_element_base_class_reflection->getTraitNames());
        $this->assertContains(RestrictiveImplementation::class, $this->restrictive_element_base_class_reflection->getTraitNames());
    }

    /**
     * Test if reverted class does not have restrictire nor permissive traits.
     */
    public function testRevertedClassImplementsRestrictiveTrait()
    {
        $this->assertNotContains(PermissiveImplementation::class, $this->reverted_element_base_class_reflection->getTraitNames());
        $this->assertNotContains(RestrictiveImplementation::class, $this->reverted_element_base_class_reflection->getTraitNames());
    }

    /**
     * Test if reverted class does not have restrictire nor permissive traits.
     */
    public function testCahngedClassImplementsRestrictiveTrait()
    {
        $this->assertNotContains(PermissiveImplementation::class, $this->changed_element_base_class_reflection->getTraitNames());
        $this->assertContains(RestrictiveImplementation::class, $this->changed_element_base_class_reflection->getTraitNames());
    }
}
