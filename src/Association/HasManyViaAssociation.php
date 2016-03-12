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
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    protected function buildGetFinderMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $intermediary_type = $structure->getType($this->intermediary_type_name);

        $order_by = $this->getOrderBy() ? '->orderBy(' . var_export($this->getOrderBy(), true) . ')' : '';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * @var \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '     private $' . $this->getFinderPropertyName() . ';';
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance.';
        $result[] = '     *';
        $result[] = '     * @return \\ActiveCollab\\DatabaseObject\\Finder';
        $result[] = '     */';
        $result[] = '    protected function ' . $this->getFinderMethodName() . '()';
        $result[] = '    {';
        $result[] = '        if (empty($this->' . $this->getFinderPropertyName() . ')) {';
        $result[] = '            $this->' . $this->getFinderPropertyName() . ' = $this->pool->find(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ')->join(' . var_export($this->getInstanceClassFrom($namespace, $intermediary_type), true) . ')->where("`' . $intermediary_type->getTableName() . '`.`' . $this->getFkFieldNameFrom($source_type) . '` = ?", $this->getId())' . $order_by . ';';
        $result[] = '        }';
        $result[] = '        return $this->' . $this->getFinderPropertyName() . ';';
        $result[] = '    }';
    }

    /**
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    public function buildAddRelatedObjectMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $intermediary_type = $structure->getType($this->intermediary_type_name);

        $target_instance_class = $this->getInstanceClassFrom($namespace, $target_type);
        $intermediary_instance_class = $this->getInstanceClassFrom($namespace, $intermediary_type);

        $longest_docs_param_type_name = max(strlen($target_instance_class), 'array|null', '$this');

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Create connection between this ' . Inflector::singularize($source_type->getName()) . ' and $object_to_add.';
        $result[] = '     *';
        $result[] = '     * @param  ' . str_pad($target_instance_class, $longest_docs_param_type_name, ' ', STR_PAD_RIGHT) . ' $object_to_add';
        $result[] = '     * @param  ' . str_pad('array|null', $longest_docs_param_type_name, ' ', STR_PAD_RIGHT) . ' $attributes';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    public function &add' . $this->getClassifiedSingleAssociationName() . '(' . $this->getInstanceClassFrom($namespace, $target_type) . ' $object_to_add, array $attributes = null)';
        $result[] = '    {';
        $result[] = '        if ($this->isNew()) {';
        $result[] = '            throw new \RuntimeException("' . ucfirst(Inflector::singularize($source_type->getName())) . ' needs to be saved first");';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        if ($object_to_add->isNew()) {';
        $result[] = '            throw new \RuntimeException("' . ucfirst(Inflector::singularize($target_type->getName())) . ' needs to be saved first");';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        $produce_attributes = [';
        $result[] = '            "' . $this->getFkFieldNameFrom($source_type) . '" => $this->getId(),';
        $result[] = '            "' . $this->getFkFieldNameFrom($target_type) . '" => $object_to_add->getId(),';
        $result[] = '        ];';
        $result[] = '        ';
        $result[] = '        if (!empty($attributes)) {';
        $result[] = '            $produce_attributes = array_merge($produce_attributes, $attributes);';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        $this->pool->produce(' . var_export($intermediary_instance_class, true) . ', $produce_attributes);';
        $result[] = '        ';
        $result[] = '        return $this;';
        $result[] = '    }';
    }

    /**
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    public function buildRemoveRelatedObjectMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $intermediary_type = $structure->getType($this->intermediary_type_name);

        $target_instance_class = $this->getInstanceClassFrom($namespace, $target_type);
        $intermediary_instance_class = $this->getInstanceClassFrom($namespace, $intermediary_type);

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Drop connection between this ' . Inflector::singularize($source_type->getName()) . ' and $object_to_remove.';
        $result[] = '     *';
        $result[] = '     * @param  ' . $target_instance_class . ' $object_to_remove';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    public function &remove' . $this->getClassifiedSingleAssociationName() . '(' . $this->getInstanceClassFrom($namespace, $target_type) . ' $object_to_remove)';
        $result[] = '    {';
        $result[] = '        if ($this->isNew()) {';
        $result[] = '            throw new \RuntimeException("' . ucfirst(Inflector::singularize($source_type->getName())) . ' needs to be saved first");';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        if ($object_to_remove->isNew()) {';
        $result[] = '            throw new \RuntimeException("' . ucfirst(Inflector::singularize($target_type->getName())) . ' needs to be saved first");';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        $intermediary_object = $this->pool->find(' . var_export($intermediary_instance_class, true) . ')->where("' . $this->getFkFieldNameFrom($source_type) . ' = ? AND ' . $this->getFkFieldNameFrom($target_type) . ' = ?", $this->getId(), $object_to_remove->getId())->first();';
        $result[] = '        ';
        $result[] = '        if ($intermediary_object instanceof ' . $intermediary_instance_class . ') {';
        $result[] = '            $this->pool->scrap($intermediary_object, true);';
        $result[] = '        }';
        $result[] = '        ';
        $result[] = '        return $this;';
        $result[] = '    }';
    }

    /**
     * @param StructureInterface $structure
     * @param TypeInterface      $source_type
     * @param TypeInterface      $target_type
     * @param string             $namespace
     * @param array              $result
     */
    public function buildClearRelatedObjectsMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $intermediary_type = $structure->getType($this->intermediary_type_name);
        $intermediary_instance_class = $this->getInstanceClassFrom($namespace, $intermediary_type);

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Drop all connections between ' . str_replace('_', ' ', $target_type->getName()) . ' and this ' . Inflector::singularize($source_type->getName()) . '.';
        $result[] = '     *';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = "    public function &clear{$this->getClassifiedAssociationName()}()";
        $result[] = '    {';
        $result[] = '        if ($objects = $this->get' . $this->getClassifiedAssociationName() . '()) {';
        $result[] = '            $object_ids = [];';
        $result[] = '            ';
        $result[] = '            $this->connection->transact(function () use ($objects, &$object_ids) {';
        $result[] = '                foreach ($objects as $object) {';
        $result[] = '                    $object_ids[] = $object->getId();';
        $result[] = '                    $object->delete(true);';
        $result[] = '                }';
        $result[] = '            });';
        $result[] = '            ';
        $result[] = '            $this->pool->forget(' . var_export($intermediary_instance_class, true) . ', $object_ids);';
        $result[] = '        }';
        $result[] = '        return $this;';
        $result[] = '    }';
    }
}
