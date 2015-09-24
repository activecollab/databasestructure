<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\Connection;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
abstract class DatabaseBuilder extends Builder
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param  Connection $connection
     * @return $this
     */
    public function &setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}