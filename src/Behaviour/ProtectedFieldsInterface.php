<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

interface ProtectedFieldsInterface
{
    /**
     * Return a list of fields that should be protected from public creation and update.
     */
    public function getProtectedFields(): array;
}
