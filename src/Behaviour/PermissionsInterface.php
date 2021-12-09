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
     *
     * @param  UserInterface $user
     * @return bool
     */
    public function canCreate(UserInterface $user);

    /**
     * Return true if the given user can view this object.
     *
     * @param  UserInterface $user
     * @return bool
     */
    public function canView(UserInterface $user);

    /**
     * Return true if the given user can edit this object.
     *
     * @param  UserInterface $user
     * @return bool
     */
    public function canEdit(UserInterface $user);

    /**
     * Return true if the given user can delete this object.
     *
     * @param  UserInterface $user
     * @return bool
     */
    public function canDelete(UserInterface $user);
}
