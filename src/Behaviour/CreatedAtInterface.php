<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\Object\ObjectInterface;

interface CreatedAtInterface extends ObjectInterface
{
    /**
     * Return value of created_at field.
     *
     * @return DateTimeValueInterface|null
     */
    public function getCreatedAt(): ?DateTimeValueInterface;

    /**
     * @param  DateTimeValueInterface|null $value
     * @return $this
     */
    public function &setCreatedAt(?DateTimeValueInterface $value);
}
