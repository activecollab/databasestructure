<?php

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
     * Set up test environment
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
    }

    /**
     * Test if base classes implement the interface
     */
    public function testBaseClassesImplementsPermissionsInterface()
    {
        $this->assertTrue($this->element_base_class_reflection->implementsInterface(PermissionsInterface::class));
        $this->assertTrue($this->restrictive_element_base_class_reflection->implementsInterface(PermissionsInterface::class));
    }

    /**
     * Test if permissive trait is added to permissive class
     */
    public function testPermissiveClassImplementsPermissiveTrait()
    {
        $this->assertContains(PermissiveImplementation::class, $this->element_base_class_reflection->getTraitNames());
        $this->assertNotContains(RestrictiveImplementation::class, $this->element_base_class_reflection->getTraitNames());
    }

    /**
     * Test if restrictive trait is added to restrictive class
     */
    public function testRestrictiveClassImplementsRestrictiveTrait()
    {
        $this->assertNotContains(PermissiveImplementation::class, $this->restrictive_element_base_class_reflection->getTraitNames());
        $this->assertContains(RestrictiveImplementation::class, $this->restrictive_element_base_class_reflection->getTraitNames());
    }
}
