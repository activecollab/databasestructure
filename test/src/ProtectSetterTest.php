<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Test\Fixtures\ProtectSetter\ProtectSetter;
use ReflectionClass;

class ProtectSetterTest extends TestCase
{
    /**
     * @var ProtectSetter
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\ProtectSetter';

    /**
     * @var ReflectionClass
     */
    private $base_user_reflection, $user_reflection;

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
    public function setUp(): void
    {
        parent::setUp();

        $this->structure = new ProtectSetter();

        if (!class_exists("{$this->namespace}\\User", false)) {
            $this->structure->build();
        }

        $this->base_user_reflection = new ReflectionClass("{$this->namespace}\\Base\\User");
        $this->user_reflection = new ReflectionClass("{$this->namespace}\\User");

        $this->base_book_reflection = new ReflectionClass("{$this->namespace}\\Base\\Book");
        $this->book_reflection = new ReflectionClass("{$this->namespace}\\Book");

        $this->base_note_reflection = new ReflectionClass("{$this->namespace}\\Base\\Note");
        $this->note_reflection = new ReflectionClass("{$this->namespace}\\Note");
    }

    /**
     * Test if field setter is not protected by default.
     */
    public function testFieldSetterIsNotProtectedByDefault()
    {
        $this->assertFalse((new StringField('awesome_field'))->getProtectSetter());
    }

    /**
     * Test if field setter can be protected.
     */
    public function testFieldSetterCanBeProtected()
    {
        $this->assertTrue((new StringField('awesome_field'))->protectSetter()->getProtectSetter());
    }

    /**
     * Test setter access level.
     */
    public function testFieldSetterAccessLevel()
    {
        $this->assertTrue($this->base_user_reflection->hasMethod('setUnprotectedSetter'));
        $this->assertTrue($this->base_user_reflection->hasMethod('setProtectedSetter'));

        $this->assertTrue($this->base_user_reflection->getMethod('setUnprotectedSetter')->isPublic());
        $this->assertTrue($this->base_user_reflection->getMethod('setProtectedSetter')->isProtected());
    }

    /**
     * Test if setter is not protected by default.
     */
    public function testAssociationSetterIsNotProtectedByDefault()
    {
        $this->assertFalse((new BelongsToAssociation('books'))->getProtectSetter());
    }

    /**
     * Test if setter can be protected.
     */
    public function testAssociationSetterCanBeProtected()
    {
        $this->assertTrue((new BelongsToAssociation('books'))->protectSetter()->getProtectSetter());
    }

    /**
     * Test setter access level.
     */
    public function testAssociationSetterAccessLevel()
    {
        $this->assertTrue($this->base_book_reflection->hasMethod('getUser'));
        $this->assertTrue($this->base_book_reflection->hasMethod('setUser'));
        $this->assertTrue($this->base_book_reflection->getMethod('getUser')->isPublic());
        $this->assertTrue($this->base_book_reflection->getMethod('setUser')->isPublic());

        $this->assertTrue($this->base_note_reflection->hasMethod('getUser'));
        $this->assertTrue($this->base_note_reflection->hasMethod('setUser'));
        $this->assertTrue($this->base_note_reflection->getMethod('getUser')->isPublic());
        $this->assertTrue($this->base_note_reflection->getMethod('setUser')->isProtected());
    }
}
