<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\User\UserInterface;

interface PermissionsInterface
{
    /**
     * Return true if the given user can persist this object.
     */
    public function canCreate(UserInterface $user): bool;

    /**
     * Return true if the given user can view this object.
     */
    public function canView(UserInterface $user): bool;

    /**
     * Return true if the given user can edit this object.
     */
    public function canEdit(UserInterface $user): bool;

    /**
     * Return true if the given user can delete this object.
     */
    public function canDelete(UserInterface $user): bool;
}
