<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
interface DatabaseBuilderInterface
{
    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * @param  ConnectionInterface $connection
     * @return $this
     */
    public function &setConnection(ConnectionInterface $connection);
}