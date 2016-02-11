<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class BaseTypeCollectionBuilder extends FileSystemBuilder
{
    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
        $base_collection_class_name = Inflector::classify($type->getName());
        $type_class_name = Inflector::classify(Inflector::singularize($type->getName()));

        $base_class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/Collection/Base/$base_collection_class_name.php" : null;

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        if ($this->getStructure()->getNamespace()) {
            $base_class_namespace = $this->getStructure()->getNamespace() . '\\Collection\\Base';
            $type_class_name = '\\' . ltrim($this->getStructure()->getNamespace(), '\\') . '\\' . $type_class_name;
        } else {
            $base_class_namespace = 'Collection\\Base';
        }

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = '/**';
        $result[] = ' * @package ' . $base_class_namespace;
        $result[] = ' */';

        $interfaces = $traits = [];

        foreach ($type->getTraits() as $interface => $implementations) {
            if ($interface != '--just-paste-trait--') {
                $interfaces[] = '\\' . ltrim($interface, '\\');
            }

            if (count($implementations)) {
                foreach ($implementations as $implementation) {
                    $traits[] = '\\' . ltrim($implementation, '\\');
                }
            }
        }

        $result[] = 'abstract class ' . $base_collection_class_name . ' extends \ActiveCollab\DatabaseObject\Collection\Type';
        $result[] = '{';
        $result[] = '    /**';
        $result[] = '     * Return type that this collection works with';
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

        $this->triggerEvent('on_class_built', [$base_collection_class_name, $base_class_build_path]);
    }
}
