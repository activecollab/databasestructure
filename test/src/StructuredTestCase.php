<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\FileSystem\Adapter\LocalAdapter;
use ActiveCollab\FileSystem\FileSystem;
use ActiveCollab\FileSystem\FileSystemInterface;
use InvalidArgumentException;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use ReflectionClass;

abstract class StructuredTestCase extends TestCase
{
    /**
     * @var string
     */
    protected $build_path;

    /**
     * @var FileSystemInterface
     */
    protected $filesystem;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var StructureInterface
     */
    protected $structure;

    /**
     * @var string
     */
    protected $built_in_namespace;

    /**
     * @var string[]
     */
    protected $type_entity_class_names;

    /**
     * @var string[]
     */
    protected $type_manager_class_names;

    /**
     * @var string[]
     */
    protected $type_collection_class_names;

    /**
     * @var LoggerAwareInterface
     */
    private $logger;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->build_path = $this->getBuildPath();
    }

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $structure_class_name = $this->getStructureClassName();
        $structure_class_file_name = $this->getStructureClassFileName();

        $this->filesystem = new FileSystem(new LocalAdapter($this->build_path));
        $this->filesystem->emptyDir('/', [$structure_class_file_name]);

        $this->logger = new Logger('DatabaseObject test');
        $this->logger->pushHandler(new TestHandler());

        $this->assertEquals([$structure_class_file_name], $this->filesystem->files('/'));
        $this->assertEquals([], $this->filesystem->subdirs('/'));

        $this->pool = new Pool($this->connection, $this->logger);

        $this->structure = new $structure_class_name();
        $this->structure->build($this->build_path, $this->connection);

        [
            'built_in_namespace' => $this->built_in_namespace,
            'type_entity_class_names' => $this->type_entity_class_names,
            'type_manager_class_names' => $this->type_manager_class_names,
            'type_collection_class_names' => $this->type_collection_class_names,
        ] = $this->buildStructure(
            $structure_class_name,
            $this->build_path,
            $this->connection,
            $this->pool
        );
    }

    public function tearDown()
    {
        $this->filesystem->emptyDir('/', [$this->getStructureClassFileName()]);

        parent::tearDown();
    }

    private function getStructureClassFileName(): string
    {
        $bits = explode('\\', $this->getStructureClassName());

        return array_pop($bits) . '.php';
    }

    public function buildStructure(
        string $structure_class_name,
        string $build_path,
        ConnectionInterface $connection,
        PoolInterface $pool
    ): array
    {
        if (!$this->isStructureClass($structure_class_name)) {
            throw new InvalidArgumentException('Valid structure class name is required.');
        }

        /** @var StructureInterface $structure */
        $structure = new $structure_class_name();
        $structure->build($build_path, $connection);

        $built_in_namespace = $this->getBuildNamespace($structure_class_name);

        $type_entity_class_names = [];
        $type_manager_class_names = [];
        $type_collection_class_names = [];

        /** @var TypeInterface $type */
        foreach ($structure->getTypes() as $type) {
            $type_entity_class_name = $built_in_namespace . '\\' . $type->getClassName() . '\\' . $type->getClassName();
            $this->assertTrue(class_exists($type_entity_class_name));

            $type_entity_class_names[$type->getName()] = $type_entity_class_name;

            $type_manager_class_name = $built_in_namespace . '\\' . $type->getClassName() . '\\' . $type->getManagerClassName();
            $this->assertTrue(class_exists($type_manager_class_name));

            $type_manager_class_names[$type->getName()] = $type_manager_class_name;

            $type_collection_class_name = $built_in_namespace . '\\' . $type->getClassName() . '\\' . $type->getCollectionClassName();
            $this->assertTrue(class_exists($type_collection_class_name));

            $type_collection_class_names[$type->getName()] = $type_collection_class_name;
        }

        foreach ($type_entity_class_names as $type_entity_class_name) {
            $pool->registerType($type_entity_class_name);
        }

        return [
            'built_in_namespace' => $built_in_namespace,
            'type_entity_class_names' => $type_entity_class_names,
            'type_manager_class_names' => $type_manager_class_names,
            'type_collection_class_names' => $type_collection_class_names,
        ];
    }

    private function isStructureClass(string $structure_class_name): bool
    {
        if (!class_exists($structure_class_name, true)) {
            return false;
        }

        return (new ReflectionClass($structure_class_name))->implementsInterface(StructureInterface::class);
    }

    private function getBuildNamespace(string $structure_class_name): string
    {
        $bits = explode('\\', ltrim($structure_class_name, '\\'));

        if (count($bits) > 1) {
            array_pop($bits);

            return implode('\\', $bits);
        } else {
            return '';
        }
    }

    /**
     * Return fully qualified structure class name.
     *
     * @return string
     */
    abstract protected function getStructureClassName(): string;

    /**
     * Return build path (usually same directory where structure class is defined).
     *
     * @return string
     */
    abstract protected function getBuildPath(): string;
}
