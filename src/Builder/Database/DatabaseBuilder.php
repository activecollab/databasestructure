<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Database;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\Builder\Builder;

abstract class DatabaseBuilder extends Builder implements DatabaseBuilderInterface
{
    private $connection;

    public function getConnection(): ?ConnectionInterface
    {
        return $this->connection;
    }

    public function setConnection(ConnectionInterface $connection): DatabaseBuilderInterface
    {
        $this->connection = $connection;

        return $this;
    }
}
