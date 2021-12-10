<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

abstract class FileSystemBuilder extends Builder implements FileSystemBuilderInterface
{
    /**
     * Build path. If empty, class will be built to memory.
     *
     * @var string
     */
    private $build_path;

    /**
     * Return build path.
     */
    public function getBuildPath(): ?string
    {
        return $this->build_path;
    }

    public function setBuildPath(?string $value): FileSystemBuilderInterface
    {
        $this->build_path = $value;

        return $this;
    }
}
