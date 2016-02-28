<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Test\Fixtures\BelongsToProtectSetter\BelongsToProtectSetter;
use ReflectionClass;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class BelongsToProtectSetterTest extends TestCase
{
    /**
     * @var BelongsToProtectSetter
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\BelongsToProtectSetter';

    /**
     * @var ReflectionClass
     */
    private $base_book_reflection, $book_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_note_reflection, $note_reflection;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new BelongsToProtectSetter();

        if (!class_exists("{$this->namespace}\\User", false)) {
            $this->structure->build();
        }

        $this->base_book_reflection = new ReflectionClass("{$this->namespace}\\Base\\Book");
        $this->book_reflection = new ReflectionClass("{$this->namespace}\\Book");

        $this->base_note_reflection = new ReflectionClass("{$this->namespace}\\Base\\Note");
        $this->note_reflection = new ReflectionClass("{$this->namespace}\\Note");
    }

    /**
     * Test if setter is not protected by default.
     */
    public function testSetterIsNotProtectedByDefault()
    {
        $this->assertFalse((new BelongsToAssociation('books'))->getProtectSetter());
    }

    /**
     * Test if setter can be protected.
     */
    public function testSetterCanBeProtected()
    {
        $this->assertTrue((new BelongsToAssociation('books'))->protectSetter()->getProtectSetter());
    }

    /**
     * Test setter access level.
     */
    public function testSetterAccessLevel()
    {
        $this->base_book_reflection->hasMethod('getUser');
        $this->base_book_reflection->hasMethod('setUser');
        $this->assertTrue($this->base_book_reflection->getMethod('getUser')->isPublic());
        $this->assertTrue($this->base_book_reflection->getMethod('setUser')->isPublic());

        $this->base_note_reflection->hasMethod('getUser');
        $this->base_note_reflection->hasMethod('setUser');
        $this->assertTrue($this->base_note_reflection->getMethod('getUser')->isPublic());
        $this->assertTrue($this->base_note_reflection->getMethod('setUser')->isProtected());
    }
}
