<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

class BaseManagerDirBuilder extends DirBuilder
{
    protected function getDirToBuildPath(string $build_path): string
    {
        return "$build_path/Manager/Base";
    }
}
