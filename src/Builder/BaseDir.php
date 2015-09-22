<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Type;
use RuntimeException;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class BaseDir extends FileSystem
{
    /**
     * @param Type $type
     */
    public function build(Type $type)
    {
        $build_path = $this->getBuildPath();

        if ($build_path) {
            if (is_dir($build_path)) {
                $build_path = rtrim($build_path, DIRECTORY_SEPARATOR);

                if (!is_dir("$build_path/Base")) {
                    $old_umask = umask(0);
                    $dir_created = mkdir("$build_path/Base");
                    umask($old_umask);

                    if ($dir_created) {
                        $this->triggerEvent('on_base_dir_created', ["$build_path/Base"]);
                    } else {
                        throw new RuntimeException("Failed to create '$build_path/Base' directory");
                    }
                }
            } else {
                throw new InvalidArgumentException("Directory '$build_path' not found");
            }
        }
    }
}