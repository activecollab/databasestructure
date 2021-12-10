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
        $result[] = '     *';
        $result[] = '     * @return string';
        $result[] = '     */';
        $result[] = '    public function getType()';
        $result[] = '    {';
        $result[] = '        return ' . var_export($type_class_name, true) . ';';
        $result[] = '    }';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($base_class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent(
            'on_class_built', [
                $base_collection_class_name,
                $base_class_build_path,
            ]
        );
    }
}
