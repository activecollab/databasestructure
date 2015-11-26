<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\User\UserInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface CreatedByRequiredInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @param  boolean       $use_cache
     * @return UserInterface
     */
    public function getCreatedBy($use_cache = true);

    /**
     * @param  UserInterface $user
     * @return $this
     */
    public function &setCreatedBy(UserInterface $user);
}
