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

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        if ($this->getStructure()->getConfig('header_comment')) {
            $result = array_merge($result, explode("\n", $this->getStructure()->getConfig('header_comment')));
            $result[] = '';
        }

        if ($this->getStructure()->getNamespace()) {
            $base_class_namespace = $this->getStructure()->getNamespace() . '\\Manager\\Base';
        } else {
            $base_class_namespace = 'Manager\\Base';
        }

        $result[] = 'declare(strict_types=1);';
        $result[] = '';
        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = 'use ActiveCollab\DatabaseObject\Entity\ManagerInterface;';

        $result[] = sprintf('interface %s extends ManagerInterface', $base_manager_interface_name);
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_manager_interface_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent(
            'on_class_built',
            [
                $base_manager_interface_name,
                $base_manager_interface_build_path
            ]
        );
    }
}
