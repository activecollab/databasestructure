<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Builder\RecordsBuilder;
use ActiveCollab\DatabaseStructure\Builder\TypeTableBuilder;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Records\RecordsStructure;

class RecordsTest extends TestCase
{
    /**
     * @var string
     */
    private $type_class_name = 'ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Records\\Record';

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var RecordsStructure
     */
    private $records_structure;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->pool = new Pool($this->connection);
        $this->records_structure = new RecordsStructure();

        if (!class_exists($this->type_class_name, false)) {
            $this->records_structure->build(null, $this->connection);
        }

        if ($this->connection->tableExists('records')) {
            $this->connection->dropTable('records');
        }

        $type_table_builder = new TypeTableBuilder($this->records_structure);
        $type_table_builder->setConnection($this->connection);
        $type_table_builder->buildType($this->records_structure->getType('records'));

        $this->assertTrue($this->connection->tableExists('records'));

        $type_triggers_builder = new RecordsBuilder($this->records_structure);
        $type_triggers_builder->setConnection($this->connection);
        $type_triggers_builder->postBuild();
    }

    public function testRecordsCount()
    {
        $this->assertSame(3, $this->connection->count('records'));

        $names = $this->connection->executeFirstColumn('SELECT `name` FROM `records`');

        $this->assertInternalType('array', $names);
        $this->assertCount(3, $names);

        $this->assertContains('Leo Tolstoy', $names);
        $this->assertContains('Alexander Pushkin', $names);
        $this->assertContains('Fyodor Dostoyevsky', $names);
    }
}
