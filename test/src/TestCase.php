<?php

namespace ActiveCollab\DatabaseStructure\Test;

use mysqli;
use ActiveCollab\DatabaseConnection\Connection;
use RuntimeException;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var mysqli
     */
    protected $link;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Set up test environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->link = new \MySQLi('localhost', 'root', '');

        if ($this->link->connect_error) {
            throw new RuntimeException('Failed to connect to database. MySQL said: ' . $this->link->connect_error);
        }

        if (!$this->link->select_db('activecollab_database_structure_test')) {
            throw new RuntimeException('Failed to select database');
        }

        $this->connection = new Connection($this->link);

        $this->connection->execute('SET foreign_key_checks = 0;');
        foreach ($this->connection->getTableNames() as $table_name) {
            $this->connection->dropTable($table_name);
        }
        $this->connection->execute('SET foreign_key_checks = 1;');

        if ($rows = $this->connection->execute('SHOW TRIGGERS')) {
            foreach ($rows as $row) {
                var_dump($row);
            }
        }
    }

    /**
     * Tear down test environment
     */
    public function tearDown()
    {
        $this->connection->execute('SET foreign_key_checks = 0;');
        foreach ($this->connection->getTableNames() as $table_name) {
            $this->connection->dropTable($table_name);
        }
        $this->connection->execute('SET foreign_key_checks = 1;');

        if ($rows = $this->connection->execute('SHOW TRIGGERS')) {
            foreach ($rows as $row) {
                var_dump($row);
            }
        }

        $this->connection = null;
        $this->link->close();

        parent::tearDown();
    }
}