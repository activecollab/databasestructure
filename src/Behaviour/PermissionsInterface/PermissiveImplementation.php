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
trait PermissiveImplementation
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canView(UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit(UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete(UserInterface $user)
    {
        return true;
    }
}
