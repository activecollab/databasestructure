<?php

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
