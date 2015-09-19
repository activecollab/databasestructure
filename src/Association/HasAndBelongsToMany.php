<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use Prophecy\Exception\InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasAndBelongsToMany implements AssociationInterface
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
     * Build class methods
     *
     * @param string $namespace
     * @param Type   $source_type
     * @param Type   $target_type
     * @param array  $result
     */
    public function buildClassMethods($namespace, Type $source_type, Type $target_type, array &$result)
    {
    }
}