<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\PolymorphInterface
 */
trait RestrictiveImplementation
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(UserInterface $user)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canView(UserInterface $user)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit(UserInterface $user)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete(UserInterface $user)
    {
        return false;
    }
}
