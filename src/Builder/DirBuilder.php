<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use InvalidArgumentException;
use RuntimeException;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
abstract class DirBuilder extends FileSystemBuilder
{
    /**
     * Execute prior to type build.
     */
    public function preBuild()
    {
        $build_path = $this->getBuildPath();

        if ($build_path) {
            if (is_dir($build_path)) {
                $build_path = rtrim($build_path, DIRECTORY_SEPARATOR);

                $dir_to_create = $this->getDirToBuildPath($build_path);

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

    /**
     * Return full path of directory that needs to be built.
     *
     * @param  string $build_path
     * @return string
     */
    abstract protected function getDirToBuildPath($build_path);
}
