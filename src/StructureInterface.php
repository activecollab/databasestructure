<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

interface StructureInterface
{
    const ADD_PERMISSIVE_PERMISSIONS = 'permissive';
    const ADD_RESTRICTIVE_PERMISSIONS = 'restrictive';

    /**
     * Get all structure type.
     *
     * @return iterable|TypeInterface[]
     */
    public function getTypes(): iterable;

    /**
     * Return type by type name.
     *
     * @param  string        $type_name
     * @return TypeInterface
     */
    public function getType($type_name): TypeInterface;

    /**
     * Return a list of initial records, indexed by the table name.
     *
     * @return RecordInterface|array
     */
    public function getRecords(): array;

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @param  string|null $namespace
     * @return $this
     */
    public function &setNamespace($namespace);

    /**
     * Return a config option value.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getConfig(string $name, $default = null);

    /**
     * Set a config option option value.
     *
     * @param  string                   $name
     * @param  mixed                    $value
     * @return $this|StructureInterface
     */
    public function &setConfig($name, $value): StructureInterface;

    /**
     * Build model at the given path.
     *
     * If $build_path is null, classes will be generated, evaled and loaded into the memory
     */
    public function build(
        string $build_path = null,
        ConnectionInterface $connection = null,
        array $event_handlers = []
    ): void;
}
