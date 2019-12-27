<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder\Directories;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;
use RuntimeException;

abstract class DirBuilder extends FileSystemBuilder
{
    public function preBuild(): void
    {
        $build_path = $this->getBuildPath();

        if ($build_path) {
            if (is_dir($build_path)) {
                $build_path = rtrim($build_path, DIRECTORY_SEPARATOR);

                $dir_to_create = $this->getDirToPreBuild($build_path);

                if (!is_dir($dir_to_create)) {
                    $old_umask = umask(0);
                    $dir_created = mkdir($dir_to_create);
                    umask($old_umask);

                    if ($dir_created) {
                        $this->triggerEvent('on_dir_created', [$dir_to_create]);
                    } else {
                        throw new RuntimeException("Failed to create '$dir_to_create' directory");
                    }
                }
            } else {
                throw new InvalidArgumentException("Directory '$build_path' not found");
            }
        }
    }

    abstract protected function getDirToPreBuild(string $build_path): string;
    abstract protected function getDirToBuildForType(string $build_path, TypeInterface $type): string;
}
