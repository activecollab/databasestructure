<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\IndexInterface;

interface InjectIndexesInsterface
{
    /**
     * Return a list of indexes.
     *
     * @return IndexInterface[]
     */
    public function getIndexes(): array;
}
