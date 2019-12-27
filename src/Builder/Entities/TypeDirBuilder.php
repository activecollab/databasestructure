<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Entities;

use ActiveCollab\DatabaseStructure\Builder\Directories\DirBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeDirBuilder extends DirBuilder
{
    protected function getDirToPreBuild(string $build_path): string
    {
        return '';
    }

    protected function getDirToBuildForType(string $build_path, TypeInterface $type): string
    {
        return $build_path . '/' . $type->getClassName();
    }
}
