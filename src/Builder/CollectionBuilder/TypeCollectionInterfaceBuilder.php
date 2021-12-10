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

class TypeCollectionInterfaceBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $collection_interface_name = $type->getCollectionInterfaceName();
        $base_collection_interface_fqn = sprintf(
            '%s\\Collection\\Base\\%s',
            $this->getStructure()->getNamespace(),
            $collection_interface_name
        );

        $class_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Collection/$collection_interface_name.php"
            : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_interface_build_skipped', [$collection_interface_name, $class_build_path]);

            return;
        }

        $collection_interface_namespace = $this->getStructure()->getNamespace()
            ? $this->getStructure()->getNamespace() . '\\Collection'
            : 'Collection';

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $result[] = "namespace $collection_interface_namespace;";
            $result[] = '';
        }
        $result[] = sprintf('use %s as Base%s;', $base_collection_interface_fqn, $collection_interface_name);
        $result[] = '';
        $result[] = sprintf('interface %s extends Base%s', $collection_interface_name, $collection_interface_name);
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_interface_built',
            [
                $collection_interface_name,
                $this->writeOrEval($class_build_path, $result),
            ]
        );
    }
}
