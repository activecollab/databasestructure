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

class BaseTypeManagerBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $base_manager_class_name = $type->getBaseManagerClassName();
        $base_manager_class_build_path = $this->getBuildPath() ? $this->getBaseManagerClassBuildPath($type) : null;

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        $types_to_use = [
            'ActiveCollab\DatabaseObject\Entity\Manager',
            $this->getTypeNamespace($type) . '\\' . $type->getManagerInterfaceName(),
            $this->getTypeNamespace($type) . '\\' . $type->getInterfaceName(),
        ];

        if ($this->getStructure()->getNamespace()) {
            $result[] = 'namespace ' . $this->getBaseNamespace($type) . ';';
            $result[] = '';

            $types_to_use[] = $this->getTypeNamespace($type) . '\\' . $type->getClassName();
        }

        $this->renderTypesToUse($types_to_use, $result);

        $result[] = sprintf(
            'abstract class %s extends Manager implements %s',
            $base_manager_class_name,
            $type->getManagerInterfaceName()
        );

        $result[] = '{';
        $result[] = '    public function getType(): string';
        $result[] = '    {';
        $result[] = '        return ' . $type->getClassName() . '::class;';
        $result[] = '    }';
        $result[] = '';

        if ($type->getPolymorph()) {
            $this->buildPolymorphProduceEntityMethod($type, $result, '    ');
        } else {
            $this->buildProduceEntityMethod($type, $result, '    ');
        }

        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_manager_class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent(
            'on_class_built',
            [
                $base_manager_class_name,
                $base_manager_class_build_path,
            ]
        );
    }

    private function buildProduceEntityMethod(TypeInterface $type, array &$result, string $indent): void
    {
        $result[] = sprintf(
            '%spublic function produce%s(array $params, bool $save = true): %s',
            $indent,
            $type->getClassName(),
            $type->getInterfaceName(),
        );
        $result[] = $indent . '{';
        $result[] = sprintf(
            '%s    return $this->pool->produce(%s::class, $params, $save);',
            $indent,
            $type->getClassName()
        );
        $result[] = $indent . '}';
        $result[] = '';
    }

    private function buildPolymorphProduceEntityMethod(TypeInterface $type, array &$result, string $indent): void
    {
        $result[] = sprintf(
            '%spublic function produce%s(string $type, array $params, bool $save = true): %s',
            $indent,
            $type->getClassName(),
            $type->getInterfaceName(),
        );
        $result[] = $indent . '{';
        $result[] = sprintf(
            '%s    return $this->pool->produce(%s::class, array_merge($params, [\'type\' => $type]), $save);',
            $indent,
            $type->getClassName()
        );
        $result[] = $indent . '}';
        $result[] = '';
    }
}
