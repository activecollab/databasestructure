<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
abstract class DatabaseBuilder extends Builder implements DatabaseBuilderInterface
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
