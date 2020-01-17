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

    protected function getOverridableClassBuildPath(TypeInterface $type, string $class): ?string
    {
        return $this->getBuildPath()
            ? sprintf('%s/%s/%s.php', $this->getBuildPath(), $type->getClassName(), $class)
            : null;
    }

    protected function getBaseClassBuildPath(TypeInterface $type, string $class): ?string
    {
        return $this->getBuildPath()
            ? sprintf('%s/%s/Base/%s.php', $this->getBuildPath(), $type->getClassName(), $class)
            : null;
    }

    // ---------------------------------------------------
    //  Type interface and class.
    // ---------------------------------------------------

    protected function getTypeClassBuildPath(TypeInterface $type): ?string
    {
        return $this->getOverridableClassBuildPath($type, $type->getClassName());
    }

    protected function getTypeInterfaceBuildPath(TypeInterface $type): ?string
    {
        return $this->getOverridableClassBuildPath($type, $type->getInterfaceName());
    }

    protected function getBaseTypeClassBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getBaseClassName());
    }

    protected function getBaseTypeInterfaceBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getBaseInterfaceName());
    }

    // ---------------------------------------------------
    //  Manager interface and class.
    // ---------------------------------------------------

    protected function getManagerClassBuildPath(TypeInterface $type): ?string
    {
        return $this->getOverridableClassBuildPath($type, $type->getManagerClassName());
    }

    protected function getManagerInterfaceBuildPath(TypeInterface $type): ?string
    {
        return $this->getOverridableClassBuildPath($type, $type->getManagerInterfaceName());
    }

    protected function getBaseManagerClassBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getBaseManagerClassName());
    }

    protected function getBaseManagerInterfaceBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getBaseManagerInterfaceName());
    }

    // ---------------------------------------------------
    //  Collection interface and class.
    // ---------------------------------------------------

    protected function getCollectionBuildPath(TypeInterface $type): ?string
    {
        return $this->getOverridableClassBuildPath($type, $type->getCollectionClassName());
    }

    protected function getBaseCollectionBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getBaseCollectionClassName());
    }

    protected function getBaseCollectionInterfaceBuildPath(TypeInterface $type): ?string
    {
        return $this->getBaseClassBuildPath($type, $type->getBaseCollectionInterfaceName());
    }
}
