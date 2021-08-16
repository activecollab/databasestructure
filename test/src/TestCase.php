<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseConnection\Connection\MysqliConnection;
use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;
use mysqli;
use RuntimeException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var mysqli
     */
    protected $link;

    /**
     * @var MysqliConnection
     */
    protected $connection;

    /**
     * @var DateTimeValueInterface|null
     */
    protected $now;

    /**
     * Set up test environment.
     */
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

        $this->setNow(new DateTimeValue());
    }

    /**
     * Tear down test environment.
     */
    public function tearDown(): void
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

        $this->connection = null;
        $this->link->close();

        $this->setNow(null);

        parent::tearDown();
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
