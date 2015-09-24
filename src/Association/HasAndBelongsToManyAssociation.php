<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasAndBelongsToManyAssociation extends Association implements AssociationInterface
{
    use AssociationInterface\Implementation;

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
     * Order releated records by
     *
     * @var string
     */
    private $order_by = null;

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
     * @return Index[]
     */
    public function getIndexes()
    {
    }

    /**
     * Return left field name
     *
     * @return string
     */
    public function getLeftFieldName()
    {
        return Inflector::singularize($this->getSourceTypeName()) . '_id';
    }

    /**
     * Return right field name
     *
     * @return string
     */
    public function getRightFieldName()
    {
        return Inflector::singularize($this->getTargetTypeName()) . '_id';
    }

    /**
     * Return left constraint name
     *
     * @return string
     */
    public function getLeftConstraintName()
    {
        return $this->getLeftFieldName() . '_constraint';
    }

    /**
     * Return right constraint name
     *
     * @return string
     */
    public function getRightConstraintName()
    {
        return $this->getRightFieldName() . '_constraint';
    }

    /**
     * Return connection table name
     *
     * @return string
     */
    public function getConnectionTableName()
    {
        return $this->getSourceTypeName() . '_' . $this->getTargetTypeName();
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