<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseConnection\BatchInsert\BatchInsert;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\FinderInterface;
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\HasAndBelongsToManyAssociatedEntitiesManager;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;

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
        return 'has_and_belongs_to_many_' . md5($this->getVerboseLeftConstraintName());
    }

    /**
     * Return verbose left constraint name.
     *
     * @return string
     */
    public function getVerboseLeftConstraintName()
    {
        return $this->getLeftFieldName() . '_for_' . $this->getConnectionTableName() . '_constraint';
    }

    /**
     * Return right constraint name.
     *
     * @return string
     */
    public function getRightConstraintName()
    {
        return 'has_and_belongs_to_many_' . md5($this->getVerboseRightConstraintName());
    }

    /**
     * Return verbose right constraint name.
     *
     * @return string
     */
    public function getVerboseRightConstraintName()
    {
        return $this->getRightFieldName() . '_for_' . $this->getConnectionTableName() . '_constraint';
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

    public function buildAssociatedEntitiesManagerConstructionLine(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    )
    {
        $namespace = $structure->getNamespace();

        if ($namespace) {
            $namespace = '\\' . ltrim($namespace, '\\');
        }

        $entity_class_name = $namespace ? $namespace . '\\' . $target_type->getEntityClassName() : $target_type->getEntityClassName();

        $result[] = $indent . var_export($this->getName(), true) . ' => new \\' . HasAndBelongsToManyAssociatedEntitiesManager::class . '(';
        $result[] = $indent . '    $this->connection,';
        $result[] = $indent . '    $this->pool,';
        $result[] = $indent . '    ' . var_export($this->getConnectionTableName(), true) . ',';
        $result[] = $indent . '    ' . var_export($this->getLeftFieldName(), true) . ',';
        $result[] = $indent . '    ' . var_export($this->getRightFieldName(), true) . ',';
        $result[] = $indent . '    ' . var_export($entity_class_name, true) . ',';
        $result[] = $indent . '    ' . var_export($this->isRequired(), true);
        $result[] = $indent . '),';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildGetFinderMethod(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        $namespace,
        array &$result
    )
    {
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance.';
        $result[] = '     *';
        $result[] = '     * @return \\' . FinderInterface::class;
        $result[] = '     */';
        $result[] = '    protected function ' . $this->getFinderMethodName() . '(): \\' . FinderInterface::class;
        $result[] = '    {';
        $result[] = '        return $this->pool';
        $result[] = '            ->find(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ')';
        $result[] = '            ->joinTable(' . var_export($this->getConnectionTableName(), true) . ')';
        $result[] = '            ->where(\'`' . $this->getConnectionTableName() . '`.`' . $this->getFkFieldNameFrom($source_type) . '` = ?\', $this->getId())';
        if ($this->getOrderBy()) {
            $result[] = '            ->orderBy(' . var_export($this->getOrderBy(), true) . ');';
        } else {
            $result[count($result) - 1] .= ';';
        }
        $result[] = '    }';
    }

    /**
     * {@inheritdoc}
     */
    public function buildAddRelatedObjectMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $target_instance_class = $this->getInstanceClassFrom($namespace, $target_type);

        $returns_and_accepts = $target_instance_class;
        if ($this->getAccepts()) {
            $returns_and_accepts = '\\' . ltrim($this->getAccepts(), '\\');
        }

        $objects_to_add_param_doscs = $returns_and_accepts . '[]';
        if ($returns_and_accepts != $target_instance_class) {
            $objects_to_add_param_doscs .= '|' . $target_instance_class . '[]';
        }

        $longest_docs_param_type_name = max(
            strlen($objects_to_add_param_doscs),
            strlen('$this')
        );

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Create connection between this ' . Inflector::singularize($source_type->getName()) . ' and one or more $objects_to_add.';
        $result[] = '     *';
        $result[] = '     * @param  ' . str_pad($objects_to_add_param_doscs, $longest_docs_param_type_name, ' ', STR_PAD_RIGHT) . ' $objects_to_add';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    public function &add' . $this->getClassifiedAssociationName() . '(' . $returns_and_accepts . ' ...$objects_to_add)';
        $result[] = '    {';
        $result[] = '        if ($this->isNew()) {';
        $result[] = '            throw new \RuntimeException(\'' . ucfirst(Inflector::singularize($source_type->getName())) . ' needs to be saved first\');';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        $batch = new \\' . BatchInsert::class . '(';
        $result[] = '            $this->connection,';
        $result[] = '            ' .  var_export($this->getConnectionTableName(), true) . ',';
        $result[] = '            [' . var_export($this->getFkFieldNameFrom($source_type), true) . ', ' . var_export($this->getFkFieldNameFrom($target_type), true) . '],';
        $result[] = '            50,';
        $result[] = '            \\' . ConnectionInterface::class . '::REPLACE';
        $result[] = '        );';
        $result[] = '';
        $result[] = '        foreach ($objects_to_add as $object_to_add) {';
        $result[] = '            if ($object_to_add->isNew()) {';
        $result[] = '                throw new \RuntimeException(\'All ' . str_replace('_', ' ', Inflector::singularize($target_type->getName())) . ' needs to be saved first\');';
        $result[] = '            }';
        $result[] = '';
        $result[] = '            $batch->insert($this->getId(), $object_to_add->getId());';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        $batch->done();';
        $result[] = '';
        $result[] = '        return $this;';
        $result[] = '    }';
    }

    /**
     * {@inheritdoc}
     */
    public function buildRemoveRelatedObjectMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $target_instance_class = $this->getInstanceClassFrom($namespace, $target_type);

        $returns_and_accepts = $target_instance_class;
        if ($this->getAccepts()) {
            $returns_and_accepts = '\\' . ltrim($this->getAccepts(), '\\');
        }

        $objects_to_remove_param_doscs = $returns_and_accepts . '[]';
        if ($returns_and_accepts != $target_instance_class) {
            $objects_to_remove_param_doscs .= '|' . $target_instance_class . '[]';
        }

        $longest_docs_param_type_name = max(
            strlen($objects_to_remove_param_doscs),
            strlen('$this')
        );

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Drop connection between this ' . Inflector::singularize($source_type->getName()) . ' and $object_to_remove.';
        $result[] = '     *';
        $result[] = '     * @param  ' . str_pad($objects_to_remove_param_doscs, $longest_docs_param_type_name, ' ', STR_PAD_RIGHT) . ' $objects_to_remove';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = '    public function &remove' . $this->getClassifiedAssociationName() . '(' . $returns_and_accepts . ' ...$objects_to_remove)';
        $result[] = '    {';
        $result[] = '        if ($this->isNew()) {';
        $result[] = '            throw new \RuntimeException(\'' . ucfirst(Inflector::singularize($source_type->getName())) . ' needs to be saved first\');';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        $ids_to_remove = [];';
        $result[] = '';
        $result[] = '        foreach ($objects_to_remove as $object_to_remove) {';
        $result[] = '            if ($object_to_remove->isNew()) {';
        $result[] = '                throw new \RuntimeException(\'All ' . str_replace('_', ' ', Inflector::singularize($target_type->getName())) . ' needs to be saved first\');';
        $result[] = '            }';
        $result[] = '';
        $result[] = '            $ids_to_remove[] = $object_to_remove->getId();';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        if (!empty($ids_to_remove)) {';
        $result[] = '            $this->connection->execute(\'DELETE FROM `' . $this->getConnectionTableName() . '` WHERE `' . $this->getFkFieldNameFrom($source_type) . '` = ? AND `' . $this->getFkFieldNameFrom($target_type) . '` IN ?\', $this->getId(), $ids_to_remove);';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        return $this;';
        $result[] = '    }';
    }

    /**
     * {@inheritdoc}
     */
    public function buildClearRelatedObjectsMethod(StructureInterface $structure, TypeInterface $source_type, TypeInterface $target_type, $namespace, array &$result)
    {
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Drop all connections between ' . str_replace('_', ' ', $target_type->getName()) . ' and this ' . Inflector::singularize($source_type->getName()) . '.';
        $result[] = '     *';
        $result[] = '     * @return $this';
        $result[] = '     */';
        $result[] = "    public function &clear{$this->getClassifiedAssociationName()}()";
        $result[] = '    {';
        $result[] = '        if ($this->isNew()) {';
        $result[] = '            throw new \RuntimeException(\'' . ucfirst(Inflector::singularize($source_type->getName())) . ' needs to be saved first\');';
        $result[] = '        }';
        $result[] = '';
        $result[] = '        $this->connection->execute(\'DELETE FROM `' . $this->getConnectionTableName() . '` WHERE `' . $this->getFkFieldNameFrom($source_type) . '` = ?\', $this->getId());';
        $result[] = '';
        $result[] = '        return $this;';
        $result[] = '    }';
    }
}
