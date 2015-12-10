<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface PermissionsInterface
{
    /**
     * Return true if the given user can view this object
     *
     * @param  UserInterface $user
     * @return boolean
     */
    public function canView(UserInterface $user);

    /**
     * Return true if the given user can edit this object
     *
     * @param  UserInterface $user
     * @return boolean
     */
    public function canEdit(UserInterface $user);

    /**
     * Return true if the given user can delete this object
     *
     * @param  UserInterface $user
     * @return boolean
     */
    public function canDelete(UserInterface $user);
}
