<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Directories;

use ActiveCollab\DatabaseStructure\TypeInterface;

class CollectionDirBuilder extends DirBuilder
{
    protected function getDirToPreBuild(string $build_path): string
    {
        return "$build_path/Collection";
    }

    protected function getDirToBuildForType(string $build_path, TypeInterface $type): string
    {
        return '';
    }
}
