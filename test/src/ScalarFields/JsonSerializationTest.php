<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Test\Fixtures\JsonSerialization\JsonSerializationStructure;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use Psr\Log\LoggerInterface;

/**
 *  @package ActiveCollab\DatabaseStructure\Test\ScalarFields
 */
class JsonSerializationTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\JsonSerialization\\KeyValue';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var JsonSerializationStructure
     */
    private $json_serialization_structure;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->pool = new Pool($this->connection, $this->createMock(LoggerInterface::class));
        $this->json_serialization_structure = new JsonSerializationStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->json_serialization_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('key_values')) {
            $this->connection->dropTable('key_values');
        }

        $type_table_builder = new TypeTableBuilder($this->json_serialization_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->json_serialization_structure->getType('key_values'));

        $this->assertTrue($this->connection->tableExists('key_values'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));
    }

    /**
     * Tear down test environment.
     */
    public function tearDown(): void
    {
        if ($this->connection->tableExists('key_values')) {
            $this->connection->dropTable('key_values');
        }

        parent::tearDown();
    }

    /**
     * Test if value field is not required.
     */
    public function testValueNotRequired()
    {
        /** @var JsonField $value_field */
        $value_field = $this->json_serialization_structure->getType('key_values')->getFields()['value'];

        $this->assertInstanceOf(JsonField::class, $value_field);
        $this->assertFalse($value_field->isRequired());
    }

    /**
     * Test if NULL is saved as NULL.
     */
    public function testNullIsSavedAsNull()
    {
        $key_value = $this->pool->produce($this->type_class_name, ['name' => 'xyz']);

        $this->assertInstanceOf($this->type_class_name, $key_value);
        $this->assertNull($key_value->getValue());

        $row = $this->connection->executeFirstRow('SELECT * FROM `key_values` WHERE `id` = ?', $key_value->getId());

        $this->assertIsArray($row);
        $this->assertEquals('xyz', $row['name']);
        $this->assertNull($row['value']);
    }

    /**
     * Test if integer value can be set.
     */
    public function testIntValueSerialization()
    {
        $key_value = $this->pool->produce($this->type_class_name, ['name' => 'xyz', 'value' => 123]);

        $this->assertInstanceOf($this->type_class_name, $key_value);
        $this->assertSame(123, $key_value->getValue());

        $key_value = $this->pool->reload($this->type_class_name, $key_value->getId());
        $this->assertSame(123, $key_value->getValue());

        $row = $this->connection->executeFirstRow('SELECT * FROM `key_values` WHERE `id` = ?', $key_value->getId());

        $this->assertIsArray($row);
        $this->assertEquals('xyz', $row['name']);
        $this->assertSame('123', $row['value']);
    }

    /**
     * Test if array is encoded when saved to the database, and properly decoded when loaded.
     */
    public function testArrayValueSerialization()
    {
        $key_value = $this->pool->produce($this->type_class_name, ['name' => 'xyz', 'value' => [1, 2, 3]]);

        $this->assertInstanceOf($this->type_class_name, $key_value);
        $this->assertSame([1, 2, 3], $key_value->getValue());

        $key_value = $this->pool->reload($this->type_class_name, $key_value->getId());
        $this->assertSame([1, 2, 3], $key_value->getValue());

        $row = $this->connection->executeFirstRow('SELECT * FROM `key_values` WHERE `id` = ?', $key_value->getId());

        $this->assertIsArray($row);
        $this->assertEquals('xyz', $row['name']);
        $this->assertSame('[1,2,3]', $row['value']);
    }

    /**
     * Test if assoc array is encoded when saved to the database, and properly decoded when loaded.
     */
    public function testAssocArrayValueSerialization()
    {
        $key_value = $this->pool->produce($this->type_class_name, ['name' => 'xyz', 'value' => ['one' => 'two']]);

        $this->assertInstanceOf($this->type_class_name, $key_value);
        $this->assertSame(['one' => 'two'], $key_value->getValue());

        $key_value = $this->pool->reload($this->type_class_name, $key_value->getId());
        $this->assertSame(['one' => 'two'], $key_value->getValue());

        $row = $this->connection->executeFirstRow('SELECT * FROM `key_values` WHERE `id` = ?', $key_value->getId());

        $this->assertIsArray($row);
        $this->assertEquals('xyz', $row['name']);
        $this->assertSame('{"one":"two"}', $row['value']);
    }
}
