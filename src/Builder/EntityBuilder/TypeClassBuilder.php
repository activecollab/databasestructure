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

class TypeClassBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $class_name = $type->getEntityClassName();

        $class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/$class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_class_build_skipped', [$class_name, $class_build_path]);

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
            $class_name,
            $class_name
        );
        $result[] = '';

        $result[] = sprintf('class %s extends Base%s', $class_name, $class_name);
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_class_built',
            [
                $class_name,
                $this->writeOrEval($class_build_path, $result),
            ]
        );
    }
}
