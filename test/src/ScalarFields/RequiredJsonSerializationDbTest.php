<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseObject\Exception\ValidationException;
use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Test\Fixtures\JsonSerialization\RequiredJsonSerializationStructure;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use Psr\Log\LoggerInterface;

/**
 *  @package ActiveCollab\DatabaseStructure\Test\ScalarFields
 */
class RequiredJsonSerializationDbTest extends DbTestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\JsonSerialization\\RequiredKeyValue';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var RequiredJsonSerializationStructure
     */
    private $required_json_serialization_structure;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->pool = new Pool($this->connection, $this->createMock(LoggerInterface::class));
        $this->required_json_serialization_structure = new RequiredJsonSerializationStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->required_json_serialization_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('required_key_values')) {
            $this->connection->dropTable('required_key_values');
        }

        $type_table_builder = new TypeTableBuilder($this->required_json_serialization_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->required_json_serialization_structure->getType('required_key_values'));

        $this->assertTrue($this->connection->tableExists('required_key_values'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));
    }

    /**
     * Tear down test environment.
     */
    public function tearDown(): void
    {
        if ($this->connection->tableExists('required_key_values')) {
            $this->connection->dropTable('required_key_values');
        }

        parent::tearDown();
    }

    /**
     * Test if value field is not required.
     */
    public function testValueIsRequired()
    {
        /** @var JsonField $value_field */
        $value_field = $this->required_json_serialization_structure->getType('required_key_values')->getFields()['value'];

        $this->assertInstanceOf(JsonField::class, $value_field);
        $this->assertTrue($value_field->isRequired());
    }

    public function testValueCantBeNull()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Validation failed: Value of 'value' is required");

        $this->pool->produce($this->type_class_name, ['name' => 'xyz']);
    }

    /**
     * Test if non-empty value can be set.
     */
    public function testValueCanBeSet()
    {
        $key_value = $this->pool->produce($this->type_class_name, ['name' => 'xyz', 'value' => 123]);
        $this->assertInstanceOf($this->type_class_name, $key_value);

        $key_value = $this->pool->reload($this->type_class_name, $key_value->getId());
        $this->assertSame(123, $key_value->getValue());
    }
}
