<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\PoolInterface;

abstract class AssociatedEntitiesManager implements AssociatedEntitiesManagerInterface
{
    protected $connection;
    protected $pool;

    public function __construct(ConnectionInterface $connection, PoolInterface $pool)
    {
        $this->connection = $connection;
        $this->pool = $pool;
    }
}
