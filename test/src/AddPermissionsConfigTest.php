<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\Test\Fixtures\AddPermissions\AddPermissionsStructure;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class AddPermissionsConfigTest extends TestCase
{
    /**
     * Test if add permissions is not set by default
     */
    public function testAddPermissionsIsNullByDefault()
    {
        $this->assertNull((new AddPermissionsStructure())->getConfig('add_permissions'));
    }

    /**
     * Test if we can change config option using a constructor argument in our test structure
     */
    public function testCanChangeAddPermissionsStructureConfigViaConstructor()
    {
        $structure = new AddPermissionsStructure(StructureInterface::ADD_PERMISSIVE_PERMISSIONS);
        $this->assertEquals(StructureInterface::ADD_PERMISSIVE_PERMISSIONS, $structure->getConfig('add_permissions'));

        $structure = new AddPermissionsStructure(StructureInterface::ADD_RESTRICTIVE_PERMISSIONS);
        $this->assertEquals(StructureInterface::ADD_RESTRICTIVE_PERMISSIONS, $structure->getConfig('add_permissions'));
    }

    /**
     * Test if structure automatically adds permissive permissions when configured to do that
     */
    public function testAddPermissivePermissions()
    {
        $structure = new AddPermissionsStructure(StructureInterface::ADD_PERMISSIVE_PERMISSIONS);

        $element = $structure->getType('elements');

        $this->assertTrue($element->getPermissions());
        $this->assertTrue($element->getPermissionsArePermissive());
    }

    /**
     * Test if structure automatically adds restrictive permissions when configured to do that
     */
    public function testAddRestrictivePermissions()
    {
        $structure = new AddPermissionsStructure(StructureInterface::ADD_RESTRICTIVE_PERMISSIONS);

        $element = $structure->getType('elements');

        $this->assertTrue($element->getPermissions());
        $this->assertFalse($element->getPermissionsArePermissive());
    }
}
