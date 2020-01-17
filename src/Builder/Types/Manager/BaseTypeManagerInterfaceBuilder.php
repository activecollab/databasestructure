<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types\Manager;

use ActiveCollab\DatabaseObject\Entity\ManagerInterface;
use ActiveCollab\DatabaseStructure\Builder\Types\TypeBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class BaseTypeManagerInterfaceBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $base_interface_name = $type->getBaseManagerInterfaceName();
        $base_interface_build_path = $this->getBaseManagerInterfaceBuildPath($type);

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        $base_class_namespace = $this->getBaseNamespace($type);

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';

        $this->renderTypesToUse(
            [
                ManagerInterface::class,
            ],
            $result
        );

        $result[] = sprintf('interface %s extends ManagerInterface', $base_interface_name);

        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_interface_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent(
            'on_class_built',
            [
                $base_interface_name,
                $base_interface_build_path,
            ]
        );
    }
}
