<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasManyAssociation extends Association implements AssociationInterface
{
    use AssociationInterface\Implementation;

    /**
     * Order releated records by
     *
     * @var string
     */
    private $order_by = null;

    /**
     * @param string $name
     * @param string $target_type_name
     */
    public function __construct($name, $target_type_name = null)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid association name");
        }

        if (empty($target_type_name)) {
            $target_type_name = $name;
        }

        $this->name = $name;
        $this->target_type_name = $target_type_name;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->order_by;
    }

    /**
     * @param  string $order_by
     * @return $this
     */
    public function &orderBy($order_by)
    {
        $this->order_by = $order_by;

        return $this;
    }

    /**
     * Build class methods
     *
     * @param string        $namespace
     * @param TypeInterface $source_type
     * @param TypeInterface $target_type
     * @param array         $result
     */
    public function buildClassMethods($namespace, TypeInterface $source_type, TypeInterface $target_type, array &$result)
    {
        if ($namespace) {
            $namespace = '\\' . ltrim($namespace, '\\');
        }

        $this->buildGetFinderMethod($namespace, $source_type, $target_type, $result);
        $this->buildCountInstancesMethod($namespace, $source_type, $target_type,$result);

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName();
        $result[] = '     *';
        $result[] = '     * @return ' . $this->getInstanceClassFrom($namespace, $target_type) . '[]';
        $result[] = '     */';
        $result[] = "    public function get{$this->getClassifiedAssociationName()}()";
        $result[] = '    {';
        $result[] = '       return $this->' . $this->getFinderMethodName() . '()->all();';
        $result[] = '    }';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . Inflector::singularize($this->getName()) . ' ID-s';
        $result[] = '     *';
        $result[] = '     * @return integer[]';
        $result[] = '     */';
        $result[] = '    public function get' . Inflector::classify(Inflector::singularize($this->getName())) . 'Ids()';
        $result[] = '    {';
        $result[] = '       return $this->' . $this->getFinderMethodName() . '()->ids();';
        $result[] = '    }';

        // addBook

        // removeBook

        // clearBooks
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

    /**
     * @var string
     */
    private $classified_association_name;

    /**
     * @return string
     */
    protected function getClassifiedAssociationName()
    {
        if (empty($this->classified_association_name)) {
            $this->classified_association_name = Inflector::classify($this->getName());
        }

        return $this->classified_association_name;
    }

    /**
     * @return string
     */
    protected function getFinderMethodName()
    {
        return "get{$this->getClassifiedAssociationName()}Finder";
    }

    /**
     * Return foreign key field name from type instance
     *
     * @param  TypeInterface $type
     * @return string
     */
    protected function getFkFieldNameFrom(TypeInterface $type)
    {
        return Inflector::singularize($type->getName()) . '_id';
    }

    /**
     * Return full instance class from namespace and type
     *
     * @param  string        $namespace
     * @param  TypeInterface $type
     * @return string
     */
    protected function getInstanceClassFrom($namespace, TypeInterface $type)
    {
        return $namespace . '\\' . Inflector::classify(Inflector::singularize($type->getName()));
    }
}
