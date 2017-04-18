<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\Base;

use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\AssociatedEntitiesManagerInterface;

interface BaseHasManyAssociatedEntitiesManagerInterface extends AssociatedEntitiesManagerInterface
{
    public function &setAssociatedEntities($values);

    public function &setAssociatedEntityIds($values);
}
