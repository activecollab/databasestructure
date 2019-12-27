<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types;

use ActiveCollab\DatabaseStructure\TypeInterface;

class BaseTypeInterfaceBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $base_interface_name = $type->getBaseInterfaceName();
        $base_class_build_path = $this->getBaseTypeInterfaceBuildPath($type);

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        $base_class_namespace = $this->getBaseNamespace($type);

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';

        $interfaces = [
            '\\' . ltrim($type->getBaseInterfaceExtends(), '\\'),
        ];

        foreach ($type->getTraits() as $interface => $implementations) {
            if ($interface != '--just-paste-trait--') {
                $interfaces[] = '\\' . ltrim($interface, '\\');
            }
        }

        $this->buildInterfaceDeclaration(
            $base_interface_name,
            $interfaces,
            '',
            $result
        );

        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent(
            'on_class_built',
            [
                $base_interface_name,
                $base_class_build_path,
            ]
        );
    }

    public function buildInterfaceDeclaration(
        string $base_interface_name,
        array $interfaces,
        string $indent,
        array &$result
    ): void
    {
        $result[] = $indent . 'interface ' . $base_interface_name . ' extends';

        if (!empty($interfaces)) {
            foreach ($interfaces as $interface) {
                $result[] = $indent . '    ' . $interface . ',';
            }

            $this->removeCommaFromLastLine($result);
        }
    }

}
