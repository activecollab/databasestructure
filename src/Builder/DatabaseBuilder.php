<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

abstract class DatabaseBuilder extends Builder implements DatabaseBuilderInterface
{
    private ?ConnectionInterface $connection = null;

    public function getConnection(): ?ConnectionInterface
    {
        return $this->connection;
    }

    public function setConnection(ConnectionInterface $connection): static
    {
        $this->connection = $connection;

        return $this;
    }
}
