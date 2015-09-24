<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\IndexInterface;
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
     * Return a list of fields that are to be added to the source type
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [];
    }

    /**
     * Return a list of indexes
     *
     * @return IndexInterface[]
     */
    public function getIndexes()
    {
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

        $target_instance_class = $namespace . '\\' . Inflector::classify(Inflector::singularize($target_type->getName()));
        $classified_association_name = Inflector::classify($this->getName());

        $fk_name = Inflector::singularize($source_type->getName()) . '_id';
        $get_finder_name = "get{$classified_association_name}Finder";
        $get_all_name = "get{$classified_association_name}";
        $get_ids_name = 'get' . Inflector::classify(Inflector::singularize($this->getName())) . 'Ids';
        $count_name = "count{$classified_association_name}";

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance';
        $result[] = '     *';
        $result[] = '     * @return \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '    private function ' . $get_finder_name . '()';
        $result[] = '    {';
        $result[] = '       return $this->pool->find(' . var_export($target_instance_class, true) . ')->where(["' . $fk_name . ' = ?", $this->getId()]);';
        $result[] = '    }';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName();
        $result[] = '     *';
        $result[] = '     * @return ' . $target_instance_class . '[]';
        $result[] = '     */';
        $result[] = '    public function ' . $get_all_name . '()';
        $result[] = '    {';
        $result[] = '       return $this->' . $get_finder_name . '()->all();';
        $result[] = '    }';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . Inflector::singularize($this->getName()) . ' ID-s';
        $result[] = '     *';
        $result[] = '     * @return integer[]';
        $result[] = '     */';
        $result[] = '    public function ' . $get_ids_name. '()';
        $result[] = '    {';
        $result[] = '       return $this->' . $get_finder_name . '()->ids();';
        $result[] = '    }';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return number of ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName();
        $result[] = '     *';
        $result[] = '     * @return integer';
        $result[] = '     */';
        $result[] = '    public function ' . $count_name . '()';
        $result[] = '    {';
        $result[] = '       return $this->pool->count(' . var_export($target_instance_class, true) . ', ["' . $fk_name . ' = ?", $this->getId()]);';
        $result[] = '    }';

        // addBook

        // removeBook

        // clearBooks
    }
}