<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use Doctrine\Common\Inflector\Inflector;

class TypesBuilder extends FileSystemBuilder
{
    public function postBuild(): void
    {
        $types_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/types.php" : null;

        if ($types_build_path) {
            $result = [];

            $result[] = '<?php';
            $result[] = '';

            if ($this->getStructure()->getConfig('header_comment')) {
                $result = array_merge($result, explode("\n", $this->getStructure()->getConfig('header_comment')));
                $result[] = '';
            }

            $namespace = $this->getStructure()->getNamespace();

            if ($namespace) {
                $namespace = ltrim($namespace, '\\');
            }

            $result[] = 'return [';

            foreach ($this->getStructure()->getTypes() as $current_type) {
                $result[] = '    ' . var_export($namespace . '\\' . Inflector::classify(Inflector::singularize($current_type->getName())), true) . ',';
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
