<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface UpdatedAtInterface
{
    /**
     * Return value of updated_at field.
     *
     * @return \ActiveCollab\DateValue\DateTimeValueInterface|null
     */
    public function getUpdatedAt();

    /**
     * @param  \ActiveCollab\DateValue\DateTimeValueInterface|null $value
     * @return $this
     */
    public function &setUpdatedAt($value);
}
