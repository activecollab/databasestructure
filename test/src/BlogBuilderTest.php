<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Blog\BlogStructure;
use ActiveCollab\Filesystem\Adapter\Local;
use ActiveCollab\Filesystem\Filesystem;

/**
 * Purpose of this test is to see if files and tables are properly build from BlogStructure
 *
 * @package ActiveCollab\DatabaseStructure\Test
 */
class BlogBuilderTest extends TestCase
{
    /**
     * @var string
     */
    private $build_path;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var BlogStructure
     */
    private $blog_structure;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->build_path = __DIR__ . '/Fixtures/Blog';
    }

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->filesystem = new Filesystem(new Local($this->build_path));
        $this->filesystem->emptyDir('/', ['BlogStructure.php']);

        $this->assertEquals(['BlogStructure.php'], $this->filesystem->files('/'));
        $this->assertEquals([], $this->filesystem->subdirs('/'));

        $this->blog_structure = new BlogStructure();
        $this->blog_structure->build($this->build_path, $this->connection);
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        $this->filesystem->emptyDir('/', ['BlogStructure.php']);

        parent::tearDown();
    }

    /**
     * Test if base type classes are properly build
     */
    public function testBuildBaseTypes()
    {
        $this->assertFileExists("$this->build_path/Base/Category.php");
        $this->assertFileExists("$this->build_path/Base/Post.php");
        $this->assertFileExists("$this->build_path/Base/Comment.php");
    }

    /**
     * Test if type classes are properly build
     */
    public function testBuildTypes()
    {
        $this->assertFileExists("$this->build_path/Category.php");
        $this->assertFileExists("$this->build_path/Post.php");
        $this->assertFileExists("$this->build_path/Comment.php");
    }

    /**
     * Test if types.php is properly generated
     */
    public function testBuildTypesPhp()
    {
        $this->assertFileExists("$this->build_path/types.php");
    }

    /**
     * Test if structure.sql is properly generated
     */
    public function testBuildStructureSql()
    {
        $this->assertFileExists("$this->build_path/structure.sql");
    }

    /**
     * Check if all tables are created
     */
    public function testTablesCreated()
    {
        $table_names = $this->connection->getTableNames();

        $this->assertContains('categories', $table_names);
        $this->assertContains('categories_posts', $table_names);
        $this->assertContains('posts', $table_names);
        $this->assertContains('comments', $table_names);
    }

    /**
     * Test indexes in categories table
     */
    public function testCategoriesIndexes()
    {
        $indexes = $this->getTableIndexes('categories');

        $this->assertInternalType('array', $indexes);
        $this->assertCount(2, $indexes);

        $this->assertContains('PRIMARY', $indexes);
        $this->assertContains('name', $indexes);
    }

    /**
     * Test indexes in posts table
     */
    public function testPostsIndexes()
    {
        $indexes = $this->getTableIndexes('posts');

        $this->assertInternalType('array', $indexes);
        $this->assertCount(2, $indexes);

        $this->assertContains('PRIMARY', $indexes);
        $this->assertContains('published_at', $indexes);
    }

    /**
     * Test indexes in comments table
     */
    public function testCommentsIndexes()
    {
        $indexes = $this->getTableIndexes('comments');

        $this->assertInternalType('array', $indexes);
        $this->assertCount(3, $indexes);

        $this->assertContains('PRIMARY', $indexes);
        $this->assertContains('created_at', $indexes);
        $this->assertContains('post_id', $indexes);
    }

    /**
     * Test indexes in categories posts connection table
     */
    public function testCategoriesPostsIndexes()
    {
        $indexes = $this->getTableIndexes('categories_posts');

        $this->assertInternalType('array', $indexes);

        $this->assertCount(2, $indexes);

        $this->assertContains('PRIMARY', $indexes);
        $this->assertContains('post_id', $indexes);
    }

    /**
     * Test constraints pointing at categories
     */
    public function testConstraintsPointingAtCategories()
    {
        $categories_constraints = $this->getConstraintsPointingAtTable('categories');

        $this->assertInternalType('array', $categories_constraints);
        $this->assertCount(1, $categories_constraints);

        $this->assertArrayHasKey('category_id_constraint', $categories_constraints);

        list ($from_table, $from_field, $to_table, $to_field) = $categories_constraints['category_id_constraint'];

        $this->assertEquals('categories_posts', $from_table);
        $this->assertEquals('category_id', $from_field);
        $this->assertEquals('categories', $to_table);
        $this->assertEquals('id', $to_field);
    }

    /**
     * Test constraints pointing at categories_posts
     */
    public function testConstraintsPointingAtCategoriesPosts()
    {
        $comments_constraints = $this->getConstraintsPointingAtTable('categories_posts');

        $this->assertInternalType('array', $comments_constraints);
        $this->assertCount(0, $comments_constraints);
    }

    /**
     * Test constraints pointing at posts
     */
    public function testConstraintsPointingAtPosts()
    {
        $posts_constraints = $this->getConstraintsPointingAtTable('posts');

        $this->assertInternalType('array', $posts_constraints);
        $this->assertCount(2, $posts_constraints);

        $this->assertArrayHasKey('post_id_constraint', $posts_constraints);

        list ($from_table, $from_field, $to_table, $to_field) = $posts_constraints['post_id_constraint'];

        $this->assertEquals('categories_posts', $from_table);
        $this->assertEquals('post_id', $from_field);
        $this->assertEquals('posts', $to_table);
        $this->assertEquals('id', $to_field);

        $this->assertArrayHasKey('comment_post_constraint', $posts_constraints);

        list ($from_table, $from_field, $to_table, $to_field) = $posts_constraints['comment_post_constraint'];

        $this->assertEquals('comments', $from_table);
        $this->assertEquals('post_id', $from_field);
        $this->assertEquals('posts', $to_table);
        $this->assertEquals('id', $to_field);
    }

    /**
     * Test constraints pointing at comments
     */
    public function testConstraintsPointingAtComments()
    {
        $comments_constraints = $this->getConstraintsPointingAtTable('comments');

        $this->assertInternalType('array', $comments_constraints);
        $this->assertCount(0, $comments_constraints);
    }

    /**
     * Return table index names
     *
     * @param  string $table_name
     * @return array
     */
    public function getTableIndexes($table_name)
    {
        $result = [];

        if ($indexes = $this->connection->execute('SHOW INDEXES IN ' . $this->connection->escapeTableName($table_name))) {
            foreach ($indexes as $index) {
                $key_name = $index['Key_name'];

                if (!in_array($key_name, $result)) {
                    $result[] = $key_name;
                }
            }
        }

        return $result;
    }

    /**
     * Return constraints pointing at $table_name
     *
     * @param  string $table_name
     * @return array
     */
    private function getConstraintsPointingAtTable($table_name)
    {
        $result = [];

        $constraints = $this->connection->execute('select
            TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
        from INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        where
            REFERENCED_TABLE_NAME = ?;', $table_name);

        if ($constraints) {
            foreach ($constraints as $constraint) {
                $result[$constraint['CONSTRAINT_NAME']] = [
                    $constraint['TABLE_NAME'],
                    $constraint['COLUMN_NAME'],
                    $constraint['REFERENCED_TABLE_NAME'],
                    $constraint['REFERENCED_COLUMN_NAME'],
                ];
            }
        }

        return $result;
    }
}