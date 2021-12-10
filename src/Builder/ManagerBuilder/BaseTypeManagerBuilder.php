<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder\ManagerBuilder;

use ActiveCollab\DatabaseStructure\Builder\FileSystemBuilder;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;

class BaseTypeManagerBuilder extends FileSystemBuilder
{
    public function buildType(TypeInterface $type): void
    {
        $base_manager_class_name = $type->getManagerClassName();
        $type_class_name = $type->getEntityClassName();

        $manager_interface_fqn = sprintf(
            '%s\\Manager\\%s',
            $this->getStructure()->getNamespace(),
            $type->getManagerInterfaceName()
        );

        $base_class_build_path = $this->getBuildPath()
            ? "{$this->getBuildPath()}/Manager/Base/$base_manager_class_name.php"
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
            $base_class_namespace = $this->getStructure()->getNamespace() . '\\Manager\\Base';
            $type_class_name = '\\' . ltrim($this->getStructure()->getNamespace(), '\\') . '\\' . $type_class_name;
        } else {
            $base_class_namespace = 'Manager\\Base';
        }

        $result[] = 'namespace ' . $base_class_namespace . ';';
        $result[] = '';
        $result[] = 'use ActiveCollab\DatabaseObject\Entity\Manager;';
        $result[] = sprintf('use %s;', $manager_interface_fqn);
        $result[] = '';
        $result[] = sprintf('abstract class %s extends Manager implements %s', $base_manager_class_name, $type->getManagerInterfaceName());
        $result[] = '{';
        $result[] = '    public function getType(): string';
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

        $this->triggerEvent('on_class_built', [$base_manager_class_name, $base_class_build_path]);
    }
}
