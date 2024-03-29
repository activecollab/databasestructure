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

class BaseTypeManagerInterfaceBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $base_manager_interface_name = $type->getManagerInterfaceName();

        $base_manager_interface_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Manager/Base/$base_manager_interface_name.php"
            : null;

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $base_class_namespace = $this->getStructure()->getNamespace() . '\\Manager\\Base';
        } else {
            $base_class_namespace = 'Manager\\Base';
        }

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = 'use ActiveCollab\DatabaseObject\Entity\ManagerInterface;';
        $result[] = '';
        $result[] = sprintf('interface %s extends ManagerInterface', $base_manager_interface_name);
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_interface_built',
            [
                $base_manager_interface_name,
                $this->writeOrEval($base_manager_interface_build_path, $result),
            ]
        );
    }
}
