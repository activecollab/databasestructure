<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseObject\FinderInterface;
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\HasManyAssociatedEntitiesManager;
use ActiveCollab\DatabaseStructure\Association\ProgramToInterfaceInterface\Implementation as ProgramToInterfaceInterfaceImplementation;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

class HasManyAssociation extends Association implements
    AssociationInterface,
    ProgramToInterfaceInterface
{
    use AssociationInterface\Implementation, ProgramToInterfaceInterfaceImplementation;

    /**
     * Order releated records by.
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

    public function buildAttributeInterception(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    )
    {
        $exported_association_name =  var_export($this->getName(), true);

        $result[] = $indent . 'case ' . $exported_association_name . ':';
        $result[] = $indent . '    $this->getAssociatedEntitiesManagers()[' . $exported_association_name . ']->addAssociatedEntities($value);';
        $result[] = $indent . '    return $this;';
        $result[] = $indent . 'case ' . var_export(Inflector::singularize($this->getName()) . '_ids', true) . ':';
        $result[] = $indent . '    $this->getAssociatedEntitiesManagers()[' . $exported_association_name . ']->addAssociatedEntityIds($value);';
        $result[] = $indent . '    return $this;';
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

        $entity_class_name = $namespace ? $namespace . '\\' . $target_type->getClassName() : $target_type->getClassName();

        $result[] = $indent . var_export($this->getName(), true) . ' => new \\' . HasManyAssociatedEntitiesManager::class . '(';
        $result[] = $indent . '    $this->connection,';
        $result[] = $indent . '    $this->pool,';
        $result[] = $indent . '    ' . var_export($target_type->getTableName(), true) . ',';
        $result[] = $indent . '    ' . var_export($this->getFkFieldNameFrom($source_type), true) . ',';
        $result[] = $indent . '    ' . var_export($entity_class_name, true);
        $result[] = $indent . '),';
    }

    public function buildClassPropertiesAndMethods(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        array &$result
    )
    {
        $namespace = $structure->getNamespace();

        if ($namespace) {
            $namespace = '\\' . ltrim($namespace, '\\');
        }

        $this->buildGetFinderMethod($structure, $source_type, $target_type, $namespace, $result);

        $target_instance_class = $this->getInstanceClassFrom($namespace, $target_type);

        $returns_and_accepts = $target_instance_class;
        if ($this->getAccepts()) {
            $returns_and_accepts = '\\' . ltrim($this->getAccepts(), '\\');
        }

        $getter_returns = 'iterable|null|' . $returns_and_accepts . '[]';

        if ($returns_and_accepts != $target_instance_class) {
            $getter_returns .= '|' . $target_instance_class . '[]';
        }

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @return ' . $getter_returns;
        $result[] = '     */';
        $result[] = "    public function get{$this->getClassifiedAssociationName()}(): ?iterable";
        $result[] = '    {';
        $result[] = '        return $this->' . $this->getFinderMethodName() . '()->all();';
        $result[] = '    }';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . Inflector::singularize($this->getName()) . ' ID-s.';
        $result[] = '     *';
        $result[] = '     * @return iterable|null|int[]';
        $result[] = '     */';
        $result[] = '    public function get' . Inflector::classify(Inflector::singularize($this->getName())) . 'Ids(): ?iterable';
        $result[] = '    {';
        $result[] = '        return $this->' . $this->getFinderMethodName() . '()->ids();';
        $result[] = '    }';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return number of ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . '.';
        $result[] = '     *';
        $result[] = '     * @return int';
        $result[] = '     */';
        $result[] = "    public function count{$this->getClassifiedAssociationName()}(): int";
        $result[] = '    {';
        $result[] = '        return $this->' . $this->getFinderMethodName() . '()->count();';
        $result[] = '    }';

        $this->buildAddRelatedObjectMethod($structure, $source_type, $target_type, $namespace, $result);
        $this->buildRemoveRelatedObjectMethod($structure, $source_type, $target_type, $namespace, $result);
        $this->buildClearRelatedObjectsMethod($structure, $source_type, $target_type, $namespace, $result);
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
        $order_by = $this->getOrderBy() ? '->orderBy(' . var_export($this->getOrderBy(), true) . ')' : '';

        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * @var \\' . FinderInterface::class;
        $result[] = '     */';
        $result[] = '    private $' . $this->getFinderPropertyName() . ';';
        $result[] = '';
        $result[] = '    /**';
        $result[] = '     * Return ' . Inflector::singularize($source_type->getName()) . ' ' . $this->getName() . ' finder instance.';
        $result[] = '     *';
        $result[] = '     * @return \\' . FinderInterface::class;
        $result[] = '     */';
        $result[] = '    protected function ' . $this->getFinderMethodName() . '(): \\' . FinderInterface::class;
        $result[] = '    {';
        $result[] = '        if (empty($this->' . $this->getFinderPropertyName() . ')) {';
        $result[] = '            $this->' . $this->getFinderPropertyName() . ' = $this->pool->find(' . var_export($this->getInstanceClassFrom($namespace, $target_type), true) . ')->where(\'`' . $this->getFkFieldNameFrom($source_type) . '` = ?\', $this->getId())' . $order_by . ';';
        $result[] = '        }';
        $result[] = '';
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
        // @TODO Implement this method
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
        // @TODO Implement this method
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
        // @TODO Implement this method
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
     * @var string
     */
    private $classified_single_association_name;

    /**
     * @return string
     */
    protected function getClassifiedSingleAssociationName()
    {
        if (empty($this->classified_single_association_name)) {
            $this->classified_single_association_name = Inflector::classify(Inflector::singularize($this->getName()));
        }

        return $this->classified_single_association_name;
    }

    /**
     * @return string
     */
    protected function getFinderMethodName()
    {
        return "get{$this->getClassifiedAssociationName()}Finder";
    }

    /**
     * @return string
     */
    protected function getFinderPropertyName()
    {
        return $this->getName() . '_finder';
    }

    /**
     * Return foreign key field name from type instance.
     *
     * @param  TypeInterface $type
     * @return string
     */
    protected function getFkFieldNameFrom(TypeInterface $type)
    {
        return Inflector::singularize($type->getName()) . '_id';
    }

    /**
     * Return full instance class from namespace and type.
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
