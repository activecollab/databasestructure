<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Trigger;

use ActiveCollab\DatabaseStructure\Trigger;

/**
 * @package ActiveCollab\DatabaseStructure\Trigger
 */
class BeforeDeleteTrigger extends Trigger
{
    /**
     * Return trigger time (before or after).
     *
     * @return string
     */
    public function getTime()
    {
        return self::BEFORE;
    }

    /**
     * Return trigger event (insert, update or delete).
     *
     * @return string
     */
    public function getEvent()
    {
        return self::DELETE;
    }
}
