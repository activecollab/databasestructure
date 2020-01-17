<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types\Collection;

use ActiveCollab\DatabaseStructure\Builder\Types\TypeBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;

class BaseTypeCollectionBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $base_collection_class_name = $type->getBaseCollectionClassName();
        $base_class_build_path = $this->getBaseCollectionBuildPath($type);

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $types_to_use = [
            'ActiveCollab\DatabaseObject\Collection\Type as TypeCollection',
            $this->getTypeNamespace($type) . '\\' . $type->getCollectionInterfaceName(),
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

        $result[] = sprintf(
            'abstract class %s extends TypeCollection implements %s',
            $base_collection_class_name,
            $type->getCollectionInterfaceName()
        );

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

        $this->triggerEvent(
            'on_class_built',
            [
                $base_collection_class_name,
                $base_class_build_path,
            ]
        );
    }
}
