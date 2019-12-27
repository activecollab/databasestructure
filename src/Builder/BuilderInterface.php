<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;

interface BuilderInterface
{
    public function getStructure(): StructureInterface;

    public function preBuild();
    public function postBuild();

    public function buildType(TypeInterface $type);

    public function registerEventHandler(string $event, callable $handler): void;
}
