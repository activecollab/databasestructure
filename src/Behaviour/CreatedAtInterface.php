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
interface CreatedAtInterface
{
    /**
     * Return object ID.
     *
     * @return int
     */
    public function getId();

    /**
     * Return value of created_at field.
     *
     * @return \ActiveCollab\DateValue\DateTimeValueInterface|null
     */
    public function getCreatedAt();

    /**
     * @param  \ActiveCollab\DateValue\DateTimeValueInterface|null $value
     * @return $this
     */
    public function &setCreatedAt($value);
}
