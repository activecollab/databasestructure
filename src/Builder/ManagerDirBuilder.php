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
class ManagerDirBuilder extends FileSystemBuilder
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

                if (!is_dir("$build_path/Manager")) {
                    $old_umask = umask(0);
                    $dir_created = mkdir("$build_path/Manager");
                    umask($old_umask);

                    if ($dir_created) {
                        $this->triggerEvent('on_dir_created', ["$build_path/Manager"]);
                    } else {
                        throw new RuntimeException("Failed to create '$build_path/Manager' directory");
                    }
                }
            } else {
                throw new InvalidArgumentException("Directory '$build_path' not found");
            }
        }
    }
}
