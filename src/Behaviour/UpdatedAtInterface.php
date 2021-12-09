<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\DateValue\DateTimeValueInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface UpdatedAtInterface
{
    /**
     * Return value of updated_at field.
     *
     * @return DateTimeValueInterface
     */
    public function getUpdatedAt(): DateTimeValueInterface;

    /**
     * @param  DateTimeValueInterface $value
     * @return $this
     */
    public function setUpdatedAt(DateTimeValueInterface $value);
}
