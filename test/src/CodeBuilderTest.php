<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Structure;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\WritersStructure;
use ReflectionClass;
use ReflectionMethod;

class CodeBuilderTest extends TestCase
{
    /**
     * @var Structure
     */
    private $structure;

    /**
     * @var string
     */
    private $namespace = '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Writers';

    /**
     * @var ReflectionClass
     */
    private $base_writers_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writers_manager_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $writers_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $writers_manager_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_books_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_books_manager_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $books_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $books_manager_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_chapters_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_chapters_manager_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $chapters_manager_reflection;

    /**
     * @var ReflectionClass
     */
    private $chapters_manager_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writers_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writers_collection_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $writers_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $writers_collection_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_books_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_books_collection_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $books_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $books_collection_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_chapters_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_chapters_collection_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $chapters_collection_reflection;

    /**
     * @var ReflectionClass
     */
    private $chapters_collection_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writer_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_writer_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $writer_reflection;

    /**
     * @var ReflectionClass
     */
    private $writer_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_book_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_book_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $book_reflection;

    /**
     * @var ReflectionClass
     */
    private $book_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_chapter_reflection;

    /**
     * @var ReflectionClass
     */
    private $base_chapter_interface_reflection;

    /**
     * @var ReflectionClass
     */
    private $chapter_reflection;

    /**
     * @var ReflectionClass
     */
    private $chapter_interface_reflection;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structure = new WritersStructure();

        if (!class_exists("{$this->namespace}\\Writer\\Writer", false)) {
            $this->structure->build();
        }

        // Managers
        $this->base_writers_manager_interface_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Base\\BaseWritersManagerInterface");
        $this->base_writers_manager_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Base\\BaseWritersManager");
        $this->writers_manager_interface_reflection = new ReflectionClass("{$this->namespace}\\Writer\\WritersManagerInterface");
        $this->writers_manager_reflection = new ReflectionClass("{$this->namespace}\\Writer\\WritersManager");

        $this->base_books_manager_interface_reflection = new ReflectionClass("{$this->namespace}\\Book\\Base\\BaseBooksManagerInterface");
        $this->base_books_manager_reflection = new ReflectionClass("{$this->namespace}\\Book\\Base\\BaseBooksManager");
        $this->books_manager_interface_reflection = new ReflectionClass("{$this->namespace}\\Book\\BooksManagerInterface");
        $this->books_manager_reflection = new ReflectionClass("{$this->namespace}\\Book\\BooksManager");

        $this->base_chapters_manager_interface_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Base\\BaseChaptersManagerInterface");
        $this->base_chapters_manager_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Base\\BaseChaptersManager");
        $this->chapters_manager_interface_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\ChaptersManagerInterface");
        $this->chapters_manager_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\ChaptersManager");

        // Collections
        $this->base_writers_collection_interface_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Base\\BaseWritersCollectionInterface");
        $this->base_writers_collection_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Base\\BaseWritersCollection");
        $this->writers_collection_interface_reflection = new ReflectionClass("{$this->namespace}\\Writer\\WritersCollectionInterface");
        $this->writers_collection_reflection = new ReflectionClass("{$this->namespace}\\Writer\\WritersCollection");

        $this->base_books_collection_interface_reflection = new ReflectionClass("{$this->namespace}\\Book\\Base\\BaseBooksCollectionInterface");
        $this->base_books_collection_reflection = new ReflectionClass("{$this->namespace}\\Book\\Base\\BaseBooksCollection");
        $this->books_collection_interface_reflection = new ReflectionClass("{$this->namespace}\\Book\\BooksCollectionInterface");
        $this->books_collection_reflection = new ReflectionClass("{$this->namespace}\\Book\\BooksCollection");

        $this->base_chapters_collection_interface_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Base\\BaseChaptersCollectionInterface");
        $this->base_chapters_collection_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Base\\BaseChaptersCollection");
        $this->chapters_collection_interface_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\ChaptersCollectionInterface");
        $this->chapters_collection_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\ChaptersCollection");

        // Types
        $this->base_writer_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Base\\BaseWriter");
        $this->base_writer_interface_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Base\\BaseWriterInterface");

        $this->writer_reflection = new ReflectionClass("{$this->namespace}\\Writer\\Writer");
        $this->writer_interface_reflection = new ReflectionClass("{$this->namespace}\\Writer\\WriterInterface");

        $this->base_book_reflection = new ReflectionClass("{$this->namespace}\\Book\\Base\\BaseBook");
        $this->base_book_interface_reflection = new ReflectionClass("{$this->namespace}\\Book\\Base\\BaseBookInterface");

        $this->book_reflection = new ReflectionClass("{$this->namespace}\\Book\\Book");
        $this->book_interface_reflection = new ReflectionClass("{$this->namespace}\\Book\\BookInterface");

        $this->base_chapter_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Base\\BaseChapter");
        $this->base_chapter_interface_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Base\\BaseChapterInterface");

        $this->chapter_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\Chapter");
        $this->chapter_interface_reflection = new ReflectionClass("{$this->namespace}\\Chapter\\ChapterInterface");
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

    public function testManagerInterfacesExtendBaseManagerInterfaces()
    {
        $this->assertTrue(
            $this->writers_manager_interface_reflection->isSubclassOf(
                $this->base_writers_manager_interface_reflection->getName()
            )
        );

        $this->assertTrue(
            $this->books_manager_interface_reflection->isSubclassOf(
                $this->base_books_manager_interface_reflection->getName()
            )
        );

        $this->assertTrue(
            $this->chapters_manager_interface_reflection->isSubclassOf(
                $this->base_chapters_manager_interface_reflection->getName()
            )
        );
    }

    /**
     * Test base manager classes implement manager interface.
     */
    public function testBaseManagerClassesImplementManagerInterfaces()
    {
        $this->assertTrue(
            $this->base_writers_manager_reflection->implementsInterface(
                $this->writers_manager_interface_reflection->getName()
            )
        );
        $this->assertTrue(
            $this->base_books_manager_reflection->implementsInterface(
                $this->books_manager_interface_reflection->getName()
            )
        );
        $this->assertTrue(
            $this->base_chapters_manager_reflection->implementsInterface(
                $this->chapters_manager_interface_reflection->getName()
            )
        );
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
        $this->assertTrue(
            $this->writers_manager_reflection->isSubclassOf(
                $this->base_writers_manager_reflection->getName()
            )
        );
        $this->assertTrue(
            $this->books_manager_reflection->isSubclassOf(
                $this->base_books_manager_reflection->getName()
            )
        );
        $this->assertTrue(
            $this->chapters_manager_reflection->isSubclassOf(
                $this->base_chapters_manager_reflection->getName()
            )
        );
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

    public function testCollectionInterfacesExtendBaseCollectionInterfaces()
    {
        $this->assertTrue(
            $this->writers_collection_interface_reflection->isSubclassOf(
                $this->base_writers_collection_interface_reflection->getName()
            )
        );

        $this->assertTrue(
            $this->books_collection_reflection->isSubclassOf(
                $this->base_books_collection_reflection->getName()
            )
        );

        $this->assertTrue(
            $this->chapters_collection_reflection->isSubclassOf(
                $this->base_chapters_collection_reflection->getName()
            )
        );
    }

    public function testBaseCollectionClassesImplementCollectionInterfaces()
    {
        $this->assertTrue(
            $this->base_writers_collection_reflection->implementsInterface(
                $this->writers_collection_interface_reflection->getName()
            )
        );

        $this->assertTrue(
            $this->base_books_collection_reflection->implementsInterface(
                $this->books_collection_interface_reflection->getName()
            )
        );

        $this->assertTrue(
            $this->base_chapters_collection_reflection->implementsInterface(
                $this->chapters_collection_interface_reflection->getName()
            )
        );
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
        $this->assertTrue($this->writers_collection_reflection->isSubclassOf("{$this->namespace}\\Writer\\Base\\BaseWritersCollection"));
        $this->assertTrue($this->books_collection_reflection->isSubclassOf("{$this->namespace}\\Book\\Base\\BaseBooksCollection"));
        $this->assertTrue($this->chapters_collection_reflection->isSubclassOf("{$this->namespace}\\Chapter\\Base\\BaseChaptersCollection"));
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
        $this->assertTrue($this->writer_reflection->isSubclassOf("{$this->namespace}\\Writer\\Base\\BaseWriter"));
        $this->assertTrue($this->book_reflection->isSubclassOf("{$this->namespace}\\Book\\Base\\BaseBook"));
        $this->assertTrue($this->chapter_reflection->isSubclassOf("{$this->namespace}\\Chapter\\Base\\BaseChapter"));
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
        $this->assertEquals(
            [
                'id',
                'name',
                'birthday',
                'is_awesome',
            ],
            $this->base_writer_reflection->getDefaultProperties()['fields']
        );

        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('getId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('setId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('getName')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('setName')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('getBirthday')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('getIsAwesome')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('setIsAwesome')
        );
    }

    public function testBooleanFieldHasShortcutGetter()
    {
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_writer_reflection->getMethod('isAwesome')
        );
    }

    public function testBookClassFields()
    {
        $this->assertEquals(
            [
                'id',
                'author_id',
                'title',
            ],
            $this->base_book_reflection->getDefaultProperties()['fields']
        );

        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_book_reflection->getMethod('getId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_book_reflection->getMethod('setId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_book_reflection->getMethod('getAuthorId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_book_reflection->getMethod('setAuthorId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_book_reflection->getMethod('getTitle')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_book_reflection->getMethod('setTitle')
        );
    }

    public function testChapterClassFields()
    {
        $this->assertEquals(
            [
                'id',
                'book_id',
                'title',
                'position',
            ],
            $this->base_chapter_reflection->getDefaultProperties()['fields']
        );

        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_chapter_reflection->getMethod('getId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_chapter_reflection->getMethod('setId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_chapter_reflection->getMethod('getBookId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_chapter_reflection->getMethod('setBookId')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_chapter_reflection->getMethod('getTitle')
        );
        $this->assertInstanceOf(
            ReflectionMethod::class,
            $this->base_chapter_reflection->getMethod('setTitle')
        );
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

        $this->assertCount(2, $default_field_values);

        $this->assertArrayHasKey('title', $default_field_values);
        $this->assertSame('', $default_field_values['title']);

        $this->assertArrayHasKey('author_id', $default_field_values);
        $this->assertSame(0, $default_field_values['author_id']);
    }

    /**
     * Test chapter class default field values.
     */
    public function testChapterClassDefaultFieldValues()
    {
        $default_field_values = $this->base_chapter_reflection->getDefaultProperties()['default_field_values'];

        $this->assertCount(3, $default_field_values);

        $this->assertArrayHasKey('book_id', $default_field_values);
        $this->assertSame(0, $default_field_values['book_id']);

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

        $this->assertIsArray($order_by);
        $this->assertCount(1, $order_by);
        $this->assertEquals('name', $order_by[0]);
    }

    /**
     * Test books type order by.
     */
    public function testBooksOrderBy()
    {
        $order_by = $this->base_book_reflection->getDefaultProperties()['order_by'];

        $this->assertIsArray($order_by);
        $this->assertCount(1, $order_by);
        $this->assertEquals('id', $order_by[0]);
    }

    /**
     * Test chapters type order by.
     */
    public function testChaptersOrderBy()
    {
        $order_by = $this->base_chapter_reflection->getDefaultProperties()['order_by'];

        $this->assertIsArray($order_by);
        $this->assertCount(1, $order_by);
        $this->assertEquals('position', $order_by[0]);
    }

    /**
     * Test if JSON serialize is properly defined in writer class.
     */
    public function testJsonSerializeIsDefinedInBookClass()
    {
        $json_serialize = $this->base_writer_reflection->getMethod('jsonSerialize');
        $this->assertEquals(
            $this->base_writer_reflection->getName(),
            $json_serialize->getDeclaringClass()->getName()
        );

        $json_serialize = $this->base_book_reflection->getMethod('jsonSerialize');
        $this->assertEquals(
            'ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\Book\Base\BaseBook',
            $json_serialize->getDeclaringClass()->getName()
        );

        $json_serialize = $this->base_chapter_reflection->getMethod('jsonSerialize');
        $this->assertEquals(
            'ActiveCollab\DatabaseStructure\Test\Fixtures\Writers\Chapter\Base\BaseChapter',
            $json_serialize->getDeclaringClass()->getName()
        );
    }
}
