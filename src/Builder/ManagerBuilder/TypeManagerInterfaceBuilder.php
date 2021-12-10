<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\ManagerBuilder;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeManagerInterfaceBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $manager_interface_name = $type->getManagerInterfaceName();
        $base_manager_interface_fqn = sprintf(
            '%s\\Manager\\Base\\%s',
            $this->getStructure()->getNamespace(),
            $manager_interface_name
        );

        $class_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Manager/$manager_interface_name.php"
            : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_interface_build_skipped', [$manager_interface_name, $class_build_path]);

            return;
        }

        $manager_interface_namespace = $this->getStructure()->getNamespace()
            ? $this->getStructure()->getNamespace() . '\\Manager'
            : 'Manager';

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $result[] = "namespace $manager_interface_namespace;";
            $result[] = '';
        }

        $result[] = sprintf('use %s as Base%s;', $base_manager_interface_fqn, $manager_interface_name);
        $result[] = '';
        $result[] = sprintf('interface %s extends Base%s', $manager_interface_name, $manager_interface_name);
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_interface_built',
            [
                $manager_interface_name,
                $this->writeOrEval($class_build_path, $result),
            ]
        );
    }
}
