<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\EntityBuilder;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeInterfaceBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $interface_name = $type->getEntityClassName() . 'Interface';

        $interface_build_path = $this->getBuildPath() ?
            sprintf("%s/%s.php", $this->getBuildPath(), $interface_name)
            : null;

        if ($interface_build_path && is_file($interface_build_path)) {
            $this->triggerEvent('on_interface_build_skipped', [$interface_name, $interface_build_path]);

            return;
        }

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $result[] = 'namespace ' . $this->getStructure()->getNamespace() . ';';
            $result[] = '';
        }

        $result[] = sprintf(
            'use %s\\Base\\%s as Base%s;',
            $this->getStructure()->getNamespace(),
            $interface_name,
            $interface_name
        );
        $result[] = '';

        $result[] = sprintf('interface %s extends Base%s', $interface_name, $interface_name);
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_class_built',
            [
                $interface_name,
                $this->writeOrEval($interface_build_path, $result),
            ]
        );
    }
}
