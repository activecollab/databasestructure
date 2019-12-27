<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

abstract class FileSystemBuilder extends Builder implements FileSystemBuilderInterface
{
    private $build_path;

    public function getBuildPath(): ?string
    {
        return $this->build_path;
    }

    public function setBuildPath(string $value)
    {
        $this->build_path = $value;

        return $this;
    }

    protected function renderHeaderComment(array &$result): void
    {
        if ($this->getStructure()->getConfig('header_comment')) {
            $result = array_merge(
                $result,
                explode(
                    "\n",
                    $this->getStructure()->getConfig('header_comment')
                )
            );
            $result[] = '';
        }
    }

    protected function removeCommaFromLastLine(array &$result): void
    {
        $last_line_num = count($result) - 1;

        if ($last_line_num >= 0) {
            $result[$last_line_num] = rtrim($result[$last_line_num], ',');
        }
    }
}
