<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
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
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param array              $result
     */
    public function buildClassMethods(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, array &$result)
    {
    }
}
