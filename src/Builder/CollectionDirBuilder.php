<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class CollectionDirBuilder extends DirBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getDirToBuildPath($build_path)
    {
        return "$build_path/Collection";
    }
}
