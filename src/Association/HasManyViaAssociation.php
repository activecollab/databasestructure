<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseObject\Pool;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasManyViaAssociation extends HasManyAssociation implements AssociationInterface
{
    /**
     * @var string
     */
    private $intermediary_type_name;

    /**
     * @param string $name
     * @param string $intermediary_type_name
     * @param string $target_type_name
     */
    public function __construct($name, $intermediary_type_name, $target_type_name = null)
    {
        parent::__construct($name, $target_type_name);

        if (empty($intermediary_type_name)) {
            throw new InvalidArgumentException("Value '$intermediary_type_name' is not a valid type name");
        }

        $this->intermediary_type_name = $intermediary_type_name;
    }

    /**
     * @param string        $namespace
     * @param TypeInterface $source_type
     * @param TypeInterface $target_type
     * @param array         $result
     */
    protected function buildGetFinderMethod($namespace, TypeInterface $source_type, TypeInterface $target_type, array &$result)
    {
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance';
        $result[] = '     *';
        $result[] = '     * @return \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '    private function ' . $this->getFinderMethodName() . '()';
        $result[] = '    {';
        $result[] = '       return $this->pool->find(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ')->where("`' . $this->getFkFieldNameFrom($source_type) . '` = ?", $this->getId());';
        $result[] = '    }';
    }

    /**
     * @param string        $namespace
     * @param TypeInterface $source_type
     * @param TypeInterface $target_type
     * @param array         $result
     */
    protected function buildCountInstancesMethod($namespace, TypeInterface $source_type, TypeInterface $target_type, array &$result)
    {
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return number of ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName();
        $result[] = '     *';
        $result[] = '     * @return integer';
        $result[] = '     */';
        $result[] = "    public function count{$this->getClassifiedAssociationName()}()";
        $result[] = '    {';
        $result[] = '       return $this->pool->count(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ', ["' . $this->getFkFieldNameFrom($source_type) . ' = ?", $this->getId()]);';
        $result[] = '    }';
    }
}
