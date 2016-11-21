<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Structure;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\WritersStructure;
use ReflectionClass;
use ReflectionMethod;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class CodeBuilderTest extends TestCase
{
    /**
     * @var Structure
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Writers\\';

    /**
     * @var ReflectionClass
     */
    private $base_writers_manager_reflection, $writers_manager_reflection, $base_books_manager_reflection, $books_manager_reflection, $base_chapters_manager_reflection, $chapters_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writers_collection_reflection, $writers_collection_reflection, $base_books_collection_reflection, $books_collection_reflection, $base_chapters_collection_reflection, $chapters_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writer_reflection, $writer_reflection, $base_book_reflection, $book_reflection, $base_chapter_reflection, $chapter_reflection;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new WritersStructure();

        if (!class_exists("{$this->namespace}Writer", false)) {
            $this->structure->build();
        }

        // Managers
        $this->base_writers_manager_reflection = new ReflectionClass("{$this->namespace}Manager\\Base\\Writers");
        $this->writers_manager_reflection = new ReflectionClass("{$this->namespace}Manager\\Writers");

        $this->base_books_manager_reflection = new ReflectionClass("{$this->namespace}Manager\\Base\\Books");
        $this->books_manager_reflection = new ReflectionClass("{$this->namespace}Manager\\Books");

        $this->base_chapters_manager_reflection = new ReflectionClass("{$this->namespace}Manager\\Base\\Chapters");
        $this->chapters_manager_reflection = new ReflectionClass("{$this->namespace}Manager\\Chapters");

        // Collections
        $this->base_writers_collection_reflection = new ReflectionClass("{$this->namespace}Collection\\Base\\Writers");
        $this->writers_collection_reflection = new ReflectionClass("{$this->namespace}Collection\\Writers");

        $this->base_books_collection_reflection = new ReflectionClass("{$this->namespace}Collection\\Base\\Books");
        $this->books_collection_reflection = new ReflectionClass("{$this->namespace}Collection\\Books");

        $this->base_chapters_collection_reflection = new ReflectionClass("{$this->namespace}Collection\\Base\\Chapters");
        $this->chapters_collection_reflection = new ReflectionClass("{$this->namespace}Collection\\Chapters");

        // Types
        $this->base_writer_reflection = new ReflectionClass("{$this->namespace}Base\\Writer");
        $this->writer_reflection = new ReflectionClass("{$this->namespace}Writer");

        $this->base_book_reflection = new ReflectionClass("{$this->namespace}Base\\Book");
        $this->book_reflection = new ReflectionClass("{$this->namespace}Book");

        $this->base_chapter_reflection = new ReflectionClass("{$this->namespace}Base\\Chapter");
        $this->chapter_reflection = new ReflectionClass("{$this->namespace}Chapter");
    }

    /**
     * Test base manager classes are abstract.
     */
    public function testBaseManagerClassesAreAbstract()
    {
        $this->assertTrue($this->base_writers_manager_reflection->isAbstract());
        $this->assertTrue($this->base_books_manager_reflection->isAbstract());
        $this->assertTrue($this->base_chapters_manager_reflection->isAbstract());
    }

    /**
     * Test manager classes are not abstract.
     */
    public function testManagerClassesAreNotAbstract()
    {
        $this->assertFalse($this->writers_manager_reflection->isAbstract());
        $this->assertFalse($this->books_manager_reflection->isAbstract());
        $this->assertFalse($this->chapters_manager_reflection->isAbstract());
    }

    /**
     * Test if manager classes extend base collection classes.
     */
    public function testManagerClassInheritance()
    {
        $this->assertTrue($this->writers_manager_reflection->isSubclassOf("{$this->namespace}Manager\\Base\\Writers"));
        $this->assertTrue($this->books_manager_reflection->isSubclassOf("{$this->namespace}Manager\\Base\\Books"));
        $this->assertTrue($this->chapters_manager_reflection->isSubclassOf("{$this->namespace}Manager\\Base\\Chapters"));
    }

    /**
     * Test base collection classes are abstract.
     */
    public function testBaseCollectionClassesAreAbstract()
    {
        $this->assertTrue($this->base_writers_collection_reflection->isAbstract());
        $this->assertTrue($this->base_books_collection_reflection->isAbstract());
        $this->assertTrue($this->base_chapters_collection_reflection->isAbstract());
    }

    /**
     * Test collection classes are not abstract.
     */
    public function testCollectionClassesAreNotAbstract()
    {
        $this->assertFalse($this->writers_collection_reflection->isAbstract());
        $this->assertFalse($this->books_collection_reflection->isAbstract());
        $this->assertFalse($this->chapters_collection_reflection->isAbstract());
    }

    /**
     * Test if collection classes extend base collection classes.
     */
    public function testCollectionClassInheritance()
    {
        $this->assertTrue($this->writers_collection_reflection->isSubclassOf("{$this->namespace}Collection\\Base\\Writers"));
        $this->assertTrue($this->books_collection_reflection->isSubclassOf("{$this->namespace}Collection\\Base\\Books"));
        $this->assertTrue($this->chapters_collection_reflection->isSubclassOf("{$this->namespace}Collection\\Base\\Chapters"));
    }

    /**
     * Test if base type classes are abstract.
     */
    public function testBaseTypeClassesAreAbstract()
    {
        $this->assertTrue($this->base_writer_reflection->isAbstract());
        $this->assertTrue($this->base_book_reflection->isAbstract());
        $this->assertTrue($this->base_chapter_reflection->isAbstract());
    }

    /**
     * Test if type classes are not abstract.
     */
    public function testTypeClassesAreNotAbstract()
    {
        $this->assertFalse($this->writer_reflection->isAbstract());
        $this->assertFalse($this->book_reflection->isAbstract());
        $this->assertFalse($this->chapter_reflection->isAbstract());
    }

    /**
     * Test inheritance.
     */
    public function testTypeClassInhritance()
    {
        $this->assertTrue($this->writer_reflection->isSubclassOf("{$this->namespace}Base\\Writer"));
        $this->assertTrue($this->book_reflection->isSubclassOf("{$this->namespace}Base\\Book"));
        $this->assertTrue($this->chapter_reflection->isSubclassOf("{$this->namespace}Base\\Chapter"));
    }

    /**
     * Test writer class table name.
     */
    public function testWriterClassTable()
    {
        $this->assertEquals('writers', $this->base_writer_reflection->getDefaultProperties()['table_name']);
    }

    /**
     * Test book class table name.
     */
    public function tesBookClassTable()
    {
        $this->assertEquals('books', $this->base_book_reflection->getDefaultProperties()['table_name']);
    }

    /**
     * Test chapter class table name.
     */
    public function testChapterClassTable()
    {
        $this->assertEquals('chapters', $this->base_chapter_reflection->getDefaultProperties()['table_name']);
    }

    /**
     * Test writer class fields.
     */
    public function testWriterClassFields()
    {
        $this->assertEquals(['id', 'name', 'birthday', 'is_awesome'], $this->base_writer_reflection->getDefaultProperties()['fields']);

        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('setId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getName'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('setName'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getBirthday'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getIsAwesome'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('setIsAwesome'));
    }

    /**
     * Test if boolean fields have shortcut getter.
     */
    public function testBooleanFieldHasShortcutGetter()
    {
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('isAwesome'));
    }

    /**
     * Test book class fields.
     */
    public function testBookClassFields()
    {
        $this->assertEquals(['id', 'author_id', 'title'], $this->base_book_reflection->getDefaultProperties()['fields']);

        $this->assertInstanceOf(ReflectionMethod::class, $this->base_book_reflection->getMethod('getId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_book_reflection->getMethod('setId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_book_reflection->getMethod('getAuthorId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_book_reflection->getMethod('setAuthorId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_book_reflection->getMethod('getTitle'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_book_reflection->getMethod('setTitle'));
    }

    /**
     * Test chapter class fileds.
     */
    public function testChapterClassFields()
    {
        $this->assertEquals(['id', 'book_id', 'title', 'position'], $this->base_chapter_reflection->getDefaultProperties()['fields']);

        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('getId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('setId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('getBookId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('setBookId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('getTitle'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('setTitle'));
    }

    /**
     * Test writer class default field values.
     */
    public function testWriterClassDefaultFieldValues()
    {
        $default_field_values = $this->base_writer_reflection->getDefaultProperties()['default_field_values'];

        $this->assertCount(2, $default_field_values);

        $this->assertArrayHasKey('name', $default_field_values);
        $this->assertSame('', $default_field_values['name']);

        $this->assertArrayHasKey('is_awesome', $default_field_values);
        $this->assertSame(true, $default_field_values['is_awesome']);
    }

    /**
     * Test book class default field values.
     */
    public function testBookClassDefaultFieldValues()
    {
        $default_field_values = $this->base_book_reflection->getDefaultProperties()['default_field_values'];

        $this->assertCount(1, $default_field_values);

        $this->assertArrayHasKey('title', $default_field_values);
        $this->assertSame('', $default_field_values['title']);
    }

    /**
     * Test chapter class default field values.
     */
    public function testChapterClassDefaultFieldValues()
    {
        $default_field_values = $this->base_chapter_reflection->getDefaultProperties()['default_field_values'];

        $this->assertCount(2, $default_field_values);

        $this->assertArrayHasKey('title', $default_field_values);
        $this->assertSame('', $default_field_values['title']);
        $this->assertArrayHasKey('position', $default_field_values);
        $this->assertSame(0, $default_field_values['position']);
    }

    /**
     * Test writers type order by.
     */
    public function testWritersOrderBy()
    {
        $order_by = $this->base_writer_reflection->getDefaultProperties()['order_by'];

        $this->assertInternalType('array', $order_by);
        $this->assertCount(1, $order_by);
        $this->assertEquals('name', $order_by[0]);
    }

    /**
     * Test books type order by.
     */
    public function testBooksOrderBy()
    {
        $order_by = $this->base_book_reflection->getDefaultProperties()['order_by'];

        $this->assertInternalType('array', $order_by);
        $this->assertCount(1, $order_by);
        $this->assertEquals('id', $order_by[0]);
    }

    /**
     * Test chapters type order by.
     */
    public function testChaptersOrderBy()
    {
        $order_by = $this->base_chapter_reflection->getDefaultProperties()['order_by'];

        $this->assertInternalType('array', $order_by);
        $this->assertCount(1, $order_by);
        $this->assertEquals('position', $order_by[0]);
    }

    /**
     * Test if JSON serialize is properly defined in writer class.
     */
    public function testJsonSerializeIsDefinedInBookClass()
    {
        $json_serialize = $this->base_writer_reflection->getMethod('jsonSerialize');
        $this->assertEquals($this->base_writer_reflection->getName(), $json_serialize->getDeclaringClass()->getName());

        $json_serialize = $this->base_book_reflection->getMethod('jsonSerialize');
        $this->assertEquals('ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\Base\Book', $json_serialize->getDeclaringClass()->getName());

        $json_serialize = $this->base_chapter_reflection->getMethod('jsonSerialize');
        $this->assertEquals('ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\Base\Chapter', $json_serialize->getDeclaringClass()->getName());
    }
}
