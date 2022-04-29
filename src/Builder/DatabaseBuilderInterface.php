<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

interface DatabaseBuilderInterface
{
    public function getConnection(): ?ConnectionInterface;
    public function setConnection(ConnectionInterface $connection): static;
}
