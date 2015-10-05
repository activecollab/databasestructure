<?php

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
    private $base_writer_reflection, $writer_reflection, $base_book_reflection, $book_reflection, $base_chapter_reflection, $chapter_reflection;

    /**
     * Set up test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new WritersStructure();

        if (!class_exists("{$this->namespace}Writer", false)) {
            $this->structure->build();
        }

        $this->base_writer_reflection = new ReflectionClass("{$this->namespace}Base\\Writer");
        $this->writer_reflection = new ReflectionClass("{$this->namespace}Writer");

        $this->base_book_reflection = new ReflectionClass("{$this->namespace}Base\\Book");
        $this->book_reflection = new ReflectionClass("{$this->namespace}Book");

        $this->base_chapter_reflection = new ReflectionClass("{$this->namespace}Base\\Chapter");
        $this->chapter_reflection = new ReflectionClass("{$this->namespace}Chapter");
    }

    /**
     * Test inheritance
     */
    public function testInhritance()
    {
        $this->assertTrue($this->writer_reflection->isSubclassOf("{$this->namespace}Base\\Writer"));
        $this->assertTrue($this->book_reflection->isSubclassOf("{$this->namespace}Base\\Book"));
        $this->assertTrue($this->chapter_reflection->isSubclassOf("{$this->namespace}Base\\Chapter"));
    }

    /**
     * Test writer class table name
     */
    public function testWriterClassTable()
    {
        $this->assertEquals('writers', $this->base_writer_reflection->getDefaultProperties()['table_name']);
    }

    /**
     * Test book class table name
     */
    public function tesBookClassTable()
    {
        $this->assertEquals('books', $this->base_book_reflection->getDefaultProperties()['table_name']);
    }

    /**
     * Test chapter class table name
     */
    public function testChapterClassTable()
    {
        $this->assertEquals('chapters', $this->base_chapter_reflection->getDefaultProperties()['table_name']);
    }

    /**
     * Test writer class fields
     */
    public function testWriterClassFields()
    {
        $this->assertEquals(['id', 'name', 'birthday'], $this->base_writer_reflection->getDefaultProperties()['fields']);

        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('setId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getName'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('setName'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('getBirthday'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_writer_reflection->getMethod('setBirthday'));
    }

    /**
     * Test book class fields
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
     * Test chapter class fileds
     */
    public function testChapterClassFields()
    {
        $this->assertEquals(['id', 'book_id', 'title','position'], $this->base_chapter_reflection->getDefaultProperties()['fields']);

        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('getId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('setId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('getBookId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('setBookId'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('getTitle'));
        $this->assertInstanceOf(ReflectionMethod::class, $this->base_chapter_reflection->getMethod('setTitle'));
    }

    /**
     * Test writer class default field values
     */
    public function testWriterClassDefaultFieldValues()
    {
        $default_field_values = $this->base_writer_reflection->getDefaultProperties()['default_field_values'];

        $this->assertCount(1, $default_field_values);

        $this->assertArrayHasKey('name', $default_field_values);
        $this->assertSame('', $default_field_values['name']);
    }

    /**
     * Test book class default field values
     */
    public function testBookClassDefaultFieldValues()
    {
        $default_field_values = $this->base_book_reflection->getDefaultProperties()['default_field_values'];

        $this->assertCount(1, $default_field_values);

        $this->assertArrayHasKey('title', $default_field_values);
        $this->assertSame('', $default_field_values['title']);
    }

    /**
     * Test chapter class default field values
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
}