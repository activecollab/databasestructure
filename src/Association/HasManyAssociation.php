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
use ActiveCollab\DatabaseStructure\Association\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\AssociationInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

class HasManyAssociation extends Association implements
    AssociationInterface,
    ProgramToInterfaceInterface,
    RequiredInterface
{
    use AssociationInterface\Implementation, ProgramToInterfaceInterfaceImplementation, RequiredInterfaceImplementation;

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

    public function getAttributes(): array
    {
        return [
            $this->getName(),
            Inflector::singularize($this->getName()) . '_ids',
        ];
    }

    public function buildAttributeInterception(
        StructureInterface $structure,
        TypeInterface $source_type,
        TypeInterface $target_type,
        string $indent,
        array &$result
    )
    {
        [
            $association_name,
            $association_ids_name,
        ] = $this->getAttributes();

        $exported_association_name =  var_export($association_name, true);
        $exported_association_ids_name = var_export($association_ids_name, true);

        $result[] = $indent . 'case ' . $exported_association_name . ':';
        $result[] = $indent . '    $this->getAssociatedEntitiesManagers()[' . $exported_association_name . ']->setAssociatedEntities($value);';
        $result[] = $indent . '    $this->recordModifiedAttribute(' . $exported_association_name . ');';
        $result[] = '';
        $result[] = $indent . '    return $this;';
        $result[] = $indent . 'case ' . $exported_association_ids_name . ':';
        $result[] = $indent . '    $this->getAssociatedEntitiesManagers()[' . $exported_association_name . ']->setAssociatedEntityIds($value);';
        $result[] = $indent . '    $this->recordModifiedAttribute(' . $exported_association_ids_name . ');';
        $result[] = '';
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

        $entity_class_name = $this->getInstanceClassFrom($namespace, $target_type);

        $result[] = $indent . var_export($this->getName(), true) . ' => new \\' . HasManyAssociatedEntitiesManager::class . '(';
        $result[] = $indent . '    $this->connection,';
        $result[] = $indent . '    $this->pool,';
        $result[] = $indent . '    ' . var_export($target_type->getTableName(), true) . ',';
        $result[] = $indent . '    ' . var_export($this->getFkFieldNameFrom($source_type), true) . ',';
        $result[] = $indent . '    ' . var_export($entity_class_name, true) . ',';
        $result[] = $indent . '    ' . var_export($this->isRequired(), true);
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
        $result[] = '            ->where(\'`' . $this->getFkFieldNameFrom($source_type) . '` = ?\', $this->getId())';

        if ($this->getOrderBy()) {
            $result[] = '            ->orderBy(' . var_export($this->getOrderBy(), true) . ');';
        } else {
            $result[count($result) - 1] .= ';';
        }
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
}
