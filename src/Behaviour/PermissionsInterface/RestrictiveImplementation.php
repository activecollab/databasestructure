<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface;

use ActiveCollab\User\UserInterface;

trait RestrictiveImplementation
{
    public function canCreate(UserInterface $user)
    {
        return false;
    }

    public function canView(UserInterface $user)
    {
        return false;
    }

    public function canEdit(UserInterface $user)
    {
        return false;
    }

    public function canDelete(UserInterface $user)
    {
        return false;
    }
}
