<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

interface StructureInterface
{
    const ADD_PERMISSIVE_PERMISSIONS = 'permissive';
    const ADD_RESTRICTIVE_PERMISSIONS = 'restrictive';

    /**
     * @return TypeInterface[]|Iterable
     */
    public function getTypes(): iterable;
    public function getType(string $type_name): TypeInterface;
    public function getRecords(): array;
    public function getNamespace(): string;
    public function setNamespace($namespace): StructureInterface;
    public function getConfig(string $name, $default = null);
    public function setConfig(string $name, $value): StructureInterface;
    public function build(
        string $build_path = null,
        ConnectionInterface $connection = null,
        array $event_handlers = []
    ): void;
}
