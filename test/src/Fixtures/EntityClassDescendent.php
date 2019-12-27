<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures;

use ActiveCollab\DatabaseStructure\Entity\Entity;

class EntityClassDescendent extends Entity
{
    protected function getAssociatedEntitiesManagers(): array
    {
        return [];
    }
}
