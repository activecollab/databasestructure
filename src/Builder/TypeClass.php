<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Type;
use Doctrine\Common\Inflector\Inflector;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class TypeClass extends FileSystem
{
    /**
     * @param Type $type
     */
    public function buildType(Type $type)
    {
        $class_name = Inflector::classify(Inflector::singularize($type->getName()));
        $base_class_name = 'Base\\' . $class_name;

        $class_build_path = $this->getBuildPath() ? "{$this->getBuildPath()}/$class_name.php" : null;

        if ($class_build_path && is_file($class_build_path)) {
            $this->triggerEvent('on_class_build_skipped', [$class_name, $class_build_path]);
            return;
        }

        $result = [];

        $result[] = "<?php";
        $result[] = '';

        if ($this->getStructure()->getNamespace()) {
            $result[] = 'namespace ' . $this->getStructure()->getNamespace() . ';';
            $result[] = '';
            $result[] = '/**';
            $result[] = ' * @package ' . $this->getStructure()->getNamespace();
            $result[] = ' */';
        }

        $result[] = 'class ' . $class_name . ' extends ' . $base_class_name;
        $result[] = '{';
        $result[] = '}';
        $result[] = '';

        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($class_build_path, $result);
        } else {
            eval(ltrim($result, '<?php'));
        }

        $this->triggerEvent('on_class_built', [$class_name, $class_build_path]);
    }
}