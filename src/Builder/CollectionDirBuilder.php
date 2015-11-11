<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use RuntimeException;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class CollectionDirBuilder extends FileSystemBuilder
{
    /**
     * Execute prior to type build
     */
    public function preBuild()
    {
        $build_path = $this->getBuildPath();

        if ($build_path) {
            if (is_dir($build_path)) {
                $build_path = rtrim($build_path, DIRECTORY_SEPARATOR);

                if (!is_dir("$build_path/Collection")) {
                    $old_umask = umask(0);
                    $dir_created = mkdir("$build_path/Collection");
                    umask($old_umask);

                    if ($dir_created) {
                        $this->triggerEvent('on_dir_created', ["$build_path/Collection"]);
                    } else {
                        throw new RuntimeException("Failed to create '$build_path/Collection' directory");
                    }
                }
            } else {
                throw new InvalidArgumentException("Directory '$build_path' not found");
            }
        }
    }
}