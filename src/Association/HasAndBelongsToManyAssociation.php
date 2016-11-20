<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasAndBelongsToManyAssociation extends HasManyAssociation implements AssociationInterface
{
    /**
     * Return left field name.
     *
     * @return string
     */
    public function getLeftFieldName()
    {
        return Inflector::singularize($this->getSourceTypeName()) . '_id';
    }

    /**
     * Return right field name.
     *
     * @return string
     */
    public function getRightFieldName()
    {
        return Inflector::singularize($this->getTargetTypeName()) . '_id';
    }

    /**
     * Return left constraint name.
     *
     * @return string
     */
    public function getLeftConstraintName()
    {
        return $this->getLeftFieldName() . '_constraint';
    }

    /**
     * Return right constraint name.
     *
     * @return string
     */
    public function getRightConstraintName()
    {
        return $this->getRightFieldName() . '_constraint';
    }

    /**
     * Return connection table name.
     *
     * @return string
     */
    public function getConnectionTableName()
    {
        $type_names = [$this->getSourceTypeName(), $this->getTargetTypeName()];
        sort($type_names);

        return implode('_', $type_names);
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function buildClassMethods(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, array &$result)
//    {
//    }

    /**
     * {@inheritdoc}
     */
    protected function buildGetFinderMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $order_by = $this->getOrderBy() ? '->orderBy(' . var_export($this->getOrderBy(), true) . ')' : '';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * @var \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '    private $' . $this->getFinderPropertyName() . ';';
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance.';
        $result[] = '     *';
        $result[] = '     * @return \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '    protected function ' . $this->getFinderMethodName() . '()';
        $result[] = '    {';
        $result[] = '        if (empty($this->' . $this->getFinderPropertyName() . ')) {';
        $result[] = '            $this->' . $this->getFinderPropertyName() . ' = $this->pool->find(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ')->joinTable(' . var_export($this->getConnectionTableName(), true) . ')->where(\'`' . $this->getConnectionTableName() . '`.`' . $this->getFkFieldNameFrom($source_type) . '` = ?\', $this->getId())' . $order_by . ';';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        return $this->' . $this->getFinderPropertyName() . ';';
        $result[] = '    }';
    }
}
