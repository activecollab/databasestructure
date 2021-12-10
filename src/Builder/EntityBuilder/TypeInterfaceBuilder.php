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
    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
        $interface_name = $type->getClassName() . 'Interface';

        $interface_build_path = $this->getBuildPath() ?
            sprintf("%s/%s.php", $this->getBuildPath(), $interface_name)
            : null;

        if ($interface_build_path && is_file($interface_build_path)) {
            $this->triggerEvent('on_interface_build_skipped', [$interface_name, $interface_build_path]);

            return;
        }

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        if ($this->getStructure()->getConfig('header_comment')) {
            $result = array_merge($result, explode("\n", $this->getStructure()->getConfig('header_comment')));
            $result[] = '';
        }

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

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

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($interface_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent('on_class_built', [$interface_name, $interface_build_path]);
    }
}
