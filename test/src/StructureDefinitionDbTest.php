<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\WritersStructure;

class StructureDefinitionDbTest extends DbTestCase
{
    /**
     * @var WritersStructure
     */
    private $structure;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->structure = new WritersStructure();
    }

    /**
     * Test if get config option returns NULL by default.
     */
    public function testGetConfigOptionReturnNullByDefault()
    {
        $this->assertNull($this->structure->getConfig('this option does not exist'));
    }

    /**
     * Test if default return value can be set for get config option.
     */
    public function testGetConfigCanReturnSpecifiedDefaultValue()
    {
        $this->assertEquals('custom default', $this->structure->getConfig('this option does not exist', 'custom default'));
    }

    /**
     * Test get set config option value.
     */
    public function testGetSetConfigOption()
    {
        $this->assertNull($this->structure->getConfig('option'));
        $this->structure->setConfig('option', 'value');
        $this->assertSame('value', $this->structure->getConfig('option'));
    }

    /**
     * Use structure's namespace by default.
     */
    public function testStructureUsesItsOwnNamespaceByDefault()
    {
        $this->assertEquals((new \ReflectionClass(WritersStructure::class))->getNamespaceName(), $this->structure->getNamespace());
    }

    /**
     * Namespace can be set to a custom value.
     */
    public function testStructureNamespaceCanBeChanged()
    {
        $this->structure->setNamespace('\\Vendor\\Project\\Model');
        $this->assertEquals('Vendor\\Project\\Model', $this->structure->getNamespace());
    }

    /**
     * Global namespace defaults to empty string.
     */
    public function testGlobalNamespaceDefaultsToEmptyString()
    {
        $this->structure->setNamespace('\\');
        $this->assertEquals('', $this->structure->getNamespace());
    }
}
