<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
abstract class DatabaseBuilder extends Builder
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param  ConnectionInterface $connection
     * @return $this
     */
    public function &setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}