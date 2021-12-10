<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

interface BuilderInterface
{
    public function preBuild(): void;
    public function buildType(TypeInterface $type): void;
    public function postBuild(): void;
    public function getStructure(): StructureInterface;
    public function registerEventHandler(string $event, callable $handler): void;
}
