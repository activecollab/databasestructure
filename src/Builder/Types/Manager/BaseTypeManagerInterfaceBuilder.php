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
                $this->getTypeNamespace($type) . '\\' . $type->getInterfaceName(),
                $this->getTypeNamespace($type) . '\\' . $type->getClassName(),
            ],
            $result
        );

        $result[] = sprintf('interface %s extends ManagerInterface', $base_interface_name);

        $result[] = '{';

        if ($type->getPolymorph()) {
            $this->buildPolymorphProduceEntityMethodSignature($type, $result, '    ');
        } else {
            $this->buildProduceEntityMethodSignature($type, $result, '    ');
        }
        $this->buildByIdGetterSignature($type, $result, '    ');
        $this->buildByIdMustGetterSignature($type, $result, '    ');

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

    private function buildProduceEntityMethodSignature(TypeInterface $type, array &$result, string $indent): void
    {
        $result[] = sprintf(
            '%spublic function produce%s(array $params, bool $save = true): %s;',
            $indent,
            $type->getClassName(),
            $type->getInterfaceName(),
        );
    }

    private function buildPolymorphProduceEntityMethodSignature(
        TypeInterface $type,
        array &$result,
        string $indent
    ): void
    {
        $result[] = sprintf(
            '%spublic function produce%s(string $type, array $params, bool $save = true): %s;',
            $indent,
            $type->getClassName(),
            $type->getInterfaceName(),
        );
    }

    private function buildByIdGetterSignature(TypeInterface $type, array &$result, string $indent): void
    {
        $result[] = sprintf(
            '%spublic function get%sById(int $id, $useCache = true): ?%s;',
            $indent,
            $type->getClassName(),
            $type->getInterfaceName(),
        );
    }

    private function buildByIdMustGetterSignature(TypeInterface $type, array &$result, string $indent): void
    {
        $result[] = sprintf(
            '%spublic function mustGet%sById(int $id, $useCache = true): %s;',
            $indent,
            $type->getClassName(),
            $type->getInterfaceName(),
        );
    }
}
