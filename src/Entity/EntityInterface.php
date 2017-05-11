<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Entity;

use ActiveCollab\DatabaseObject\Entity\EntityInterface as BaseEntityInterface;

interface EntityInterface extends BaseEntityInterface
{
    public function getIdsFromAssociationAttributes(string $association_name): array;
}
