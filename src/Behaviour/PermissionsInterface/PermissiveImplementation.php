<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface;

use ActiveCollab\User\UserInterface;

trait PermissiveImplementation
{
    public function canCreate(UserInterface $user)
    {
        return true;
    }

    public function canView(UserInterface $user)
    {
        return true;
    }

    public function canEdit(UserInterface $user)
    {
        return true;
    }

    public function canDelete(UserInterface $user)
    {
        return true;
    }
}
