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

class TypeCollectionBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $collection_class_name = $type->getCollectionClassName();
        $base_class_name = 'Base\\' . $collection_class_name;

        $class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/Collection/$collection_class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_class_build_skipped', [$collection_class_name, $class_build_path]);

            return;
        }

        $collection_class_namespace = $this->getStructure()->getNamespace() ? $this->getStructure()->getNamespace() . '\\Collection' : 'Collection';

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $result[] = "namespace $collection_class_namespace;";
            $result[] = '';
        }

        $result[] = 'class ' . $collection_class_name . ' extends ' . $base_class_name;
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_class_built', [
                $collection_class_name,
                $this->writeOrEval($class_build_path, $result),
            ]
        );
    }
}
