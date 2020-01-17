<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types\Manager;

use ActiveCollab\DatabaseStructure\Builder\Types\TypeBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeManagerInterfaceBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $interface_name = $type->getManagerInterfaceName();
        $base_interface_name = 'Base\\' . $type->getBaseManagerInterfaceName();

        $interface_build_path = $this->getManagerInterfaceBuildPath($type);

        if ($interface_build_path && is_file($interface_build_path)) {
            $this->triggerEvent(
                'on_class_build_skipped',
                [
                    $interface_name,
                    $interface_build_path,
                ]
            );

            return;
        }

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        if ($this->getStructure()->getNamespace()) {
            $result[] = 'namespace ' . $this->getTypeNamespace($type) . ';';
            $result[] = '';
        }

        $result[] = 'interface ' . $interface_name . ' extends ' . $base_interface_name;
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($interface_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent(
            'on_class_built',
            [
                $interface_name,
                $interface_build_path,
            ]
        );
    }
}
