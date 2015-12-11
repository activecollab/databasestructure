<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\IndexInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
interface InjectIndexesInsterface
{
    /**
     * Return a list of indexes
     *
     * @return IndexInterface[]
     */
    public function getIndexes();
}
