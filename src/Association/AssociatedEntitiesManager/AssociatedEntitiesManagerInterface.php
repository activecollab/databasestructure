<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager;

interface AssociatedEntitiesManagerInterface
{
    public function afterInsert(int $entity_id);

    public function afterUpdate(int $entity_id, array $modifications);

    public function beforeDelete(int $entity_id);
}
