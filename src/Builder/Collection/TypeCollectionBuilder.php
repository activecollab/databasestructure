<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder\Collection;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class TypeCollectionBuilder extends FileSystemBuilder
{
    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
        $collection_class_name = $type->getCollectionClassName();
        $base_class_name = 'Base\\' . $collection_class_name;

        $class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/Collection/$collection_class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_class_build_skipped', [$collection_class_name, $class_build_path]);

            return;
        }

        $collection_class_namespace = $this->getStructure()->getNamespace() ? $this->getStructure()->getNamespace() . '\\Collection' : 'Collection';

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
            $result[] = "namespace $collection_class_namespace;";
            $result[] = '';
            $result[] = '/**';
            $result[] = ' * @package ' . $collection_class_namespace;
            $result[] = ' */';
        }

        $result[] = 'class ' . $collection_class_name . ' extends ' . $base_class_name;
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent('on_class_built', [$collection_class_name, $class_build_path]);
    }
}
