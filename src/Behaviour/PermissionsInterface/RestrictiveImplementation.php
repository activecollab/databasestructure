<?php

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
