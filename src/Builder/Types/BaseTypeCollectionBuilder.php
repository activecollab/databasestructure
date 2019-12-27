<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types;

use ActiveCollab\DatabaseStructure\TypeInterface;

class BaseTypeCollectionBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $base_collection_class_name = $type->getManagerClassName();
        $base_class_build_path = $this->getBaseManagerBuildPath($type);

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $types_to_use = [
            'ActiveCollab\DatabaseObject\Collection\Type as TypeCollection',
        ];

        if ($this->getStructure()->getNamespace()) {
            $result[] = 'namespace ' . $this->getBaseNamespace($type) . ';';
            $result[] = '';

            $types_to_use[] = $this->getTypeNamespace($type) . '\\' . $type->getClassName();
        }

        if (!empty($types_to_use)) {
            sort($types_to_use);

            foreach ($types_to_use as $type_to_use) {
                $result[] = 'use ' . $type_to_use . ';';
            }

            $result[] = '';
        }

        $interfaces = [];
        $traits = [];

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

        $result[] = 'abstract class ' . $base_collection_class_name . ' extends TypeCollection';
        $result[] = '{';
        $result[] = '    /**';
        $result[] = '     * Return type that this collection works with.';
        $result[] = '     *';
        $result[] = '     * @return string';
        $result[] = '     */';
        $result[] = '    public function getType()';
        $result[] = '    {';
        $result[] = '        return ' . $type->getClassName() . '::class;';
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
