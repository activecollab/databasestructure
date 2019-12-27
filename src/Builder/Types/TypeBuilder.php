<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

abstract class TypeBuilder extends FileSystemBuilder
{
    protected function getTypeNamespace(TypeInterface $type): ?string
    {
        return $this->getStructure()->getNamespace()
            ? $this->getStructure()->getNamespace() . '\\' . $type->getClassName()
            : 'Base';
    }

    protected function getBaseNamespace(TypeInterface $type): ?string
    {
        return $this->getStructure()->getNamespace()
            ? $this->getStructure()->getNamespace() . '\\' . $type->getClassName() . '\\Base'
            : 'Base';
    }

    protected function getBaseTypeBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getClassName());
    }

    protected function getBaseManagerBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getManagerClassName());
    }

    protected function getBaseCollectionBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getCollectionClassName());
    }

    protected function getBaseClassBuildPath(TypeInterface $type, string $class): ?string
    {
        return $this->getBuildPath()
            ? sprintf('%s/%s/Base/%s.php', $this->getBuildPath(), $type->getClassName(), $class)
            : null;
    }
}