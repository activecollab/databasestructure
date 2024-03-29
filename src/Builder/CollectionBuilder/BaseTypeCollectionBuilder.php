<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\CollectionBuilder;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class BaseTypeCollectionBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $base_collection_class_name = $type->getCollectionClassName();
        $type_class_name = $type->getEntityClassName();

        $collection_interface_fqn = sprintf(
            '%s\\Collection\\%s',
            $this->getStructure()->getNamespace(),
            $type->getCollectionInterfaceName()
        );

        $base_class_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Collection/Base/$base_collection_class_name.php"
            : null;

        $result = $this->openPhpFile();

        if ($this->getStructure()->getNamespace()) {
            $base_class_namespace = $this->getStructure()->getNamespace() . '\\Collection\\Base';
            $type_class_name = '\\' . ltrim($this->getStructure()->getNamespace(), '\\') . '\\' . $type_class_name;
        } else {
            $base_class_namespace = 'Collection\\Base';
        }

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = 'use ActiveCollab\DatabaseObject\Collection\Type as TypeCollection;';
        $result[] = sprintf('use %s;', $collection_interface_fqn);
        $result[] = '';
        $result[] = sprintf('abstract class %s extends TypeCollection implements %s', $base_collection_class_name, $type->getCollectionInterfaceName());
        $result[] = '{';
        $result[] = '    /**';
        $result[] = '     * Return type that this collection works with.';
        $result[] = '     */';
        $result[] = '    public function getType(): string';
        $result[] = '    {';
        $result[] = '        return ' . var_export($type_class_name, true) . ';';
        $result[] = '    }';
        $result[] = '}';
        $result[] = '';

        $this->triggerEvent(
            'on_class_built', [
                $base_collection_class_name,
                $this->writeOrEval($base_class_build_path, $result),
            ]
        );
    }
}
