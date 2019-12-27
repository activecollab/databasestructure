<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Directories;

class BaseManagerDirBuilder extends DirBuilder
{
    protected function getDirToPreBuild(string $build_path): array
    {
        return [
            "$build_path/Manager/Base",
        ];
    }
}
