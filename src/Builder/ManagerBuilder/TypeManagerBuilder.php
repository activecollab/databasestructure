<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder\ManagerBuilder;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeManagerBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $manager_class_name = $type->getManagerClassName();
        $base_class_name = 'Base\\' . $manager_class_name;

        $class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/Manager/$manager_class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_class_build_skipped', [$manager_class_name, $class_build_path]);

            return;
        }

        $manager_class_namespace = $this->getStructure()->getNamespace() ? $this->getStructure()->getNamespace() . '\\Manager' : 'Manager';

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $result[] = "namespace $manager_class_namespace;";
            $result[] = '';
        }

        $result[] = 'class ' . $manager_class_name . ' extends ' . $base_class_name;
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_class_built',
            [
                $manager_class_name,
                $this->writeOrEval($class_build_path, $result),
            ]
        );
    }
}
