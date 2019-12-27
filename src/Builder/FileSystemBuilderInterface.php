<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

interface FileSystemBuilderInterface
{
    public function getBuildPath(): ?string;
    public function setBuildPath(string $value);
}
