<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Blog\BlogStructure;
use ActiveCollab\FileSystem\Adapter\LocalAdapter;
use ActiveCollab\FileSystem\FileSystem;

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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->build_path = __DIR__ . '/Fixtures/Blog';
    }

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = new FileSystem(new LocalAdapter($this->build_path));
        $this->filesystem->emptyDir('/', ['BlogStructure.php']);

        $this->assertEquals(['BlogStructure.php'], $this->filesystem->files('/'));
        $this->assertEquals([], $this->filesystem->subdirs('/'));

        $this->blog_structure = new BlogStructure();
        $this->blog_structure->build($this->build_path, $this->connection);
    }

    public function tearDown()
    {
        $this->filesystem->emptyDir('/', ['BlogStructure.php']);

        parent::tearDown();
    }

    public function testDirectories()
    {
        $this->assertFileExists("$this->build_path/Category");
        $this->assertFileExists("$this->build_path/Category/Base");
        $this->assertFileExists("$this->build_path/Post");
        $this->assertFileExists("$this->build_path/Post/Base");
        $this->assertFileExists("$this->build_path/Comment");
        $this->assertFileExists("$this->build_path/Comment/Base");
    }

    /**
     * Test if base type classes are properly build.
     */
    public function testBuildBaseTypes()
    {
        $this->assertFileExists("$this->build_path/Category/Base/Category.php");
        $this->assertFileExists("$this->build_path/Post/Base/Post.php");
        $this->assertFileExists("$this->build_path/Comment/Base/Comment.php");
    }

    /**
     * Test if type classes are properly build.
     */
    public function testBuildTypes()
    {
        $this->assertFileExists("$this->build_path/Category/Category.php");
        $this->assertFileExists("$this->build_path/Post/Post.php");
        $this->assertFileExists("$this->build_path/Comment/Comment.php");
    }

    /**
     * Test if base type collections are properly build.
     */
    public function testBuildBaseTypeCollections()
    {
        $this->assertFileExists("$this->build_path/Category/Base/CategoriesCollection.php");
        $this->assertFileExists("$this->build_path/Post/Base/PostsCollection.php");
        $this->assertFileExists("$this->build_path/Comment/Base/CommentsCollection.php");
    }

    /**
     * Test if type collections are properly build.
     */
    public function testBuildTypeCollections()
    {
        $this->assertFileExists("$this->build_path/Category/CategoriesCollection.php");
        $this->assertFileExists("$this->build_path/Post/PostsCollection.php");
        $this->assertFileExists("$this->build_path/Comment/CommentsCollection.php");
    }

    /**
     * Test if types.php is properly generated.
     */
    public function testBuildTypesPhp()
    {
        $this->assertFileExists("$this->build_path/types.php");
    }

    /**
     * Test if structure.sql and initial_data.sql files are properly generated.
     */
    public function testBuildSqlFiles()
    {
        $this->assertFileExists("$this->build_path/SQL/structure.sql");
        $this->assertFileExists("$this->build_path/SQL/initial_data.sql");
    }

    /**
     * Check if all tables are created.
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
     * Test indexes in categories table.
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
     * Test indexes in posts table.
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
     * Test indexes in comments table.
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
     * Test indexes in categories posts connection table.
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
     * Test constraints pointing at categories.
     */
    public function testConstraintsPointingAtCategories()
    {
        $categories_constraints = $this->getConstraintsPointingAtTable('categories');

        $this->assertInternalType('array', $categories_constraints);
        $this->assertCount(1, $categories_constraints);

        $constraint_name = 'has_and_belongs_to_many_' . md5('category_id_for_categories_posts_constraint');

        $this->assertArrayHasKey($constraint_name, $categories_constraints);

        list($from_table, $from_field, $to_table, $to_field) = $categories_constraints[$constraint_name];

        $this->assertEquals('categories_posts', $from_table);
        $this->assertEquals('category_id', $from_field);
        $this->assertEquals('categories', $to_table);
        $this->assertEquals('id', $to_field);
    }

    /**
     * Test constraints pointing at categories_posts.
     */
    public function testConstraintsPointingAtCategoriesPosts()
    {
        $comments_constraints = $this->getConstraintsPointingAtTable('categories_posts');

        $this->assertInternalType('array', $comments_constraints);
        $this->assertCount(0, $comments_constraints);
    }

    /**
     * Test constraints pointing at posts.
     */
    public function testConstraintsPointingAtPosts()
    {
        $posts_constraints = $this->getConstraintsPointingAtTable('posts');

        $this->assertInternalType('array', $posts_constraints);
        $this->assertCount(2, $posts_constraints);

        $constraint_name = 'has_and_belongs_to_many_' . md5('post_id_for_categories_posts_constraint');

        $this->assertArrayHasKey($constraint_name, $posts_constraints);

        list($from_table, $from_field, $to_table, $to_field) = $posts_constraints[$constraint_name];

        $this->assertEquals('categories_posts', $from_table);
        $this->assertEquals('post_id', $from_field);
        $this->assertEquals('posts', $to_table);
        $this->assertEquals('id', $to_field);

        $constraint_name2 = 'belongs_to_' . md5('comment_post_constraint');

        $this->assertArrayHasKey($constraint_name2, $posts_constraints);

        list($from_table, $from_field, $to_table, $to_field) = $posts_constraints[$constraint_name2];

        $this->assertEquals('comments', $from_table);
        $this->assertEquals('post_id', $from_field);
        $this->assertEquals('posts', $to_table);
        $this->assertEquals('id', $to_field);
    }

    /**
     * Test constraints pointing at comments.
     */
    public function testConstraintsPointingAtComments()
    {
        $comments_constraints = $this->getConstraintsPointingAtTable('comments');

        $this->assertInternalType('array', $comments_constraints);
        $this->assertCount(0, $comments_constraints);
    }

    /**
     * Return table index names.
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
     * Return constraints pointing at $table_name.
     *
     * @param  string $table_name
     * @return array
     */
    private function getConstraintsPointingAtTable($table_name)
    {
        $result = [];

        $constraints = $this->connection->execute('SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = ?;', $table_name);

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
