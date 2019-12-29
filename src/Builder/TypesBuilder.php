<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

class TypesBuilder extends FileSystemBuilder
{
    public function postBuild(): void
    {
        $types_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/types.php" : null;

        if ($types_build_path) {
            $result = [];

            $result[] = '<?php';
            $result[] = '';

            $this->renderHeaderComment($result);

            $result[] = 'declare(strict_types=1);';
            $result[] = '';

            $namespace = $this->getStructure()->getNamespace();

            if ($namespace) {
                $namespace = ltrim($namespace, '\\');
            }

            $types_to_use = [];
            foreach ($this->getStructure()->getTypes() as $current_type) {
                $types_to_use[] = $namespace . '\\' . $current_type->getClassName() . '\\' . $current_type->getClassName();
            }
            sort($types_to_use);

            foreach ($types_to_use as $type_to_use) {
                $result[] = sprintf('use %s;', $type_to_use);
            }

            $result[] = '';
            $result[] = 'return [';

            foreach ($this->getStructure()->getTypes() as $current_type) {
                $result[] = '    ' . $current_type->getClassName() . '::class,';
            }

            $result[] = '];';
            $result[] = '';

            $result = implode("\n", $result);

            if (is_file($types_build_path) && file_get_contents($types_build_path) === $result) {
                return;
            } else {
                file_put_contents($types_build_path, $result);
                $this->triggerEvent('on_types_built', [$types_build_path]);
            }
        }
    }
}
