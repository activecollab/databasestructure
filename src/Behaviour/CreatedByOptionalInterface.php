<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface CreatedByOptionalInterface extends CreatedByInterface
{
    /**
     * @param  boolean       $use_cache
     * @return UserInterface
     */
    public function getCreatedBy($use_cache = true);

    /**
     * @param  UserInterface|null $user
     * @return $this
     */
    public function &setCreatedBy(UserInterface $user = null);
}
