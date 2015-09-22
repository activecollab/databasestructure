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
    private $build_path;

    /**
     * @var Filesystem
     */
    private $filesystem;

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

//    public function testBuildStructureSql()
//    {
//
//    }
}