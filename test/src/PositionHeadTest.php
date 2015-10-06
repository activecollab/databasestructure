<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\PositionHead\PositionHeadStructure;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseObject\PoolInterface;

/**
 * Purpose of this test is to see if files and tables are properly build from BlogStructure
 *
 * @package ActiveCollab\DatabaseStructure\Test
 */
class PositionHeadTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\PositionHead\\PositionHeadEntry';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var PositionHeadStructure
     */
    private $position_head_structure;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->pool = new Pool($this->connection);
        $this->position_head_structure = new PositionHeadStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->position_head_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('position_head_entries')) {
            $this->connection->dropTable('position_head_entries');
        }

        $type_table_builder = new TypeTableBuilder($this->position_head_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->position_head_structure->getType('position_head_entries'));

        $this->assertTrue($this->connection->tableExists('position_head_entries'));

        $this->pool->registerType($this->type_class_name);

        $this->assertTrue($this->pool->isTypeRegistered($this->type_class_name));
    }

    /**
     * Tear down test environment
     */
    public function tearDown()
    {
        if ($this->connection->tableExists('position_head_entries')) {
            $this->connection->dropTable('position_head_entries');
        }

        parent::tearDown();
    }

    /**
     * Test first record
     */
    public function testFirstRecord()
    {
        /** @var PositionInterface $entry */
        $entry = $this->pool->produce($this->type_class_name);

        $this->assertInstanceOf($this->type_class_name, $entry);

        $this->assertEquals(1, $entry->getPosition());
    }

    /**
     * Test if all new records go to the begining of the list
     */
    public function testNewRecordsGoToHead()
    {
        /** @var PositionInterface $entry1 */
        /** @var PositionInterface $entry2 */
        /** @var PositionInterface $entry3 */
        $entry1 = $this->pool->produce($this->type_class_name);
        $entry2 = $this->pool->produce($this->type_class_name);
        $entry3 = $this->pool->produce($this->type_class_name);

        $this->assertInstanceOf($this->type_class_name, $entry1);
        $this->assertInstanceOf($this->type_class_name, $entry2);
        $this->assertInstanceOf($this->type_class_name, $entry3);

        $this->assertEquals(1, $entry1->getPosition());
        $this->assertEquals(1, $entry2->getPosition());
        $this->assertEquals(1, $entry3->getPosition());
    }
}