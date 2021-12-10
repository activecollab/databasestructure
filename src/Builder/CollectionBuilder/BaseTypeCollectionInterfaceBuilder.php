<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\CollectionBuilder;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class BaseTypeCollectionInterfaceBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $base_collection_interface_name = $type->getCollectionInterfaceName();

        $base_class_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Collection/Base/$base_collection_interface_name.php"
            : null;

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $base_class_namespace = $this->getStructure()->getNamespace() . '\\Collection\\Base';
        } else {
            $base_class_namespace = 'Collection\\Base';
        }

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = 'use ActiveCollab\DatabaseObject\CollectionInterface;';
        $result[] = '';
        $result[] = sprintf('interface %s extends CollectionInterface', $base_collection_interface_name);
        $result[] = '{';
        $result[] = '    /**';
        $result[] = '     * Return type that this collection works with.';
        $result[] = '     *';
        $result[] = '     * @return string';
        $result[] = '     */';
        $result[] = '    public function getType();';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_interface_built', [
                $base_collection_interface_name,
                $this->writeOrEval($base_class_build_path, $result),
            ]
        );
    }
}
