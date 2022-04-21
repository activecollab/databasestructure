<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Base;

use ActiveCollab\DatabaseConnection\Connection\MysqliConnection;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;
use mysqli;
use RuntimeException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class DbTestCase extends BaseTestCase
{
    protected mysqli $link;
    protected ?ConnectionInterface $connection = null;
    protected ?DateTimeValueInterface $now = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->link = new \MySQLi('localhost', 'root', $this->getValidMySqlPassword());

        if ($this->link->connect_error) {
            throw new RuntimeException('Failed to connect to database. MySQL said: ' . $this->link->connect_error);
        }

        if (!$this->link->select_db('activecollab_database_structure_test')) {
            throw new RuntimeException('Failed to select database');
        }

        $this->connection = new MysqliConnection($this->link);

        $this->dropTablesAndTriggers();

        $this->setNow(new DateTimeValue());
    }

    /**
     * Tear down test environment.
     */
    public function tearDown(): void
    {
        $this->dropTablesAndTriggers();

        $this->connection = null;
        $this->link->close();

        $this->setNow(null);

        parent::tearDown();
    }

    private function dropTablesAndTriggers(): void
    {
        if ($triggers = $this->connection->execute('SHOW TRIGGERS')) {
            foreach ($triggers as $trigger) {
                $this->connection->execute('DROP TRIGGER ' . $this->connection->escapeFieldName($trigger['Trigger']));
            }
        }

        $this->connection->execute('SET foreign_key_checks = 0;');
        foreach ($this->connection->getTableNames() as $table_name) {
            $this->connection->dropTable($table_name);
        }
        $this->connection->execute('SET foreign_key_checks = 1;');
    }

    /**
     * @return DateTimeValueInterface
     */
    protected function getNow()
    {
        return $this->now;
    }

    /**
     * @param DateTimeValueInterface|null $now
     */
    protected function setNow(DateTimeValueInterface $now = null)
    {
        $this->now = $now;
        DateTimeValue::setTestNow($this->now);
    }

    protected function getValidMySqlPassword(): string
    {
        return (string) getenv('DATABASE_CONNECTION_TEST_PASSWORD');
    }
}
