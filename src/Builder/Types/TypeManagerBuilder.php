<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\Types;

use ActiveCollab\DatabaseStructure\TypeInterface;

class TypeManagerBuilder extends TypeBuilder
{
    public function buildType(TypeInterface $type)
    {
        $manager_class_name = $type->getManagerClassName();
        $base_class_name = 'Base\\' . $manager_class_name;

        $class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/$manager_class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_class_build_skipped', [$manager_class_name, $class_build_path]);

            return;
        }

        $result = [];

        $result[] = '<?php';
        $result[] = '';

        $this->renderHeaderComment($result);

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        if ($this->getStructure()->getNamespace()) {
            $result[] = "namespace " . $this->getTypeNamespace($type) . ";";
            $result[] = '';
        }

        $result[] = 'class ' . $manager_class_name . ' extends ' . $base_class_name;
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent('on_class_built', [$manager_class_name, $class_build_path]);
    }
}