<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

interface PolymorphInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param  string $value
     * @return $this
     */
    public function setType(string $value);
}
