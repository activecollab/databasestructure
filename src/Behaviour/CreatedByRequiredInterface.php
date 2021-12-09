<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\User\UserInterface;

interface CreatedByRequiredInterface extends CreatedByInterface
{
    /**
     * @param  bool          $use_cache
     * @return UserInterface
     */
    public function getCreatedBy($use_cache = true);

    /**
     * @param  UserInterface $user
     * @return $this
     */
    public function setCreatedBy(UserInterface $user);
}
