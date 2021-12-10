<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Association\InjectFieldsInsterface;
use ActiveCollab\DatabaseStructure\Association\InjectIndexesInsterface;
use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface\PermissiveImplementation;
use ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface\RestrictiveImplementation;
use ActiveCollab\DatabaseStructure\Behaviour\PolymorphInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PolymorphInterface\Implementation as PolymorphInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface\Implementation as ProtectedFieldsInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Entity\Entity;
use ActiveCollab\DatabaseStructure\Entity\EntityInterface;
use ActiveCollab\DatabaseStructure\Field\Composite\CompositeField as CompositeField;
use ActiveCollab\DatabaseStructure\Field\GeneratedFieldsInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField as IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;
use BadMethodCallException;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use ReflectionClass;

class Type implements TypeInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Return type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @var string
     */
    private $table_name;

    /**
     * @return string
     */
    public function getTableName()
    {
        if (empty($this->table_name)) {
            $this->table_name = $this->getName();
        }

        return $this->table_name;
    }

    /**
     * @param  string $table_name
     * @return $this
     */
    public function &setTableName($table_name)
    {
        if (empty($table_name)) {
            throw new InvalidArgumentException("Value '$table_name' is not a valid table name");
        }

        $this->table_name = $table_name;

        return $this;
    }

    /**
     * @var bool
     */
    private $polymorph = false;

    /**
     * @return bool
     */
    public function getPolymorph()
    {
        return $this->polymorph;
    }

    /**
     * Set this model to be polymorph (type field is added and used to store instance's class name).
     *
     * @param  bool  $value
     * @return $this
     */
    public function &polymorph($value = true)
    {
        $this->polymorph = (bool) $value;

        if ($this->polymorph) {
            $this->addTrait(PolymorphInterface::class, PolymorphInterfaceImplementation::class);
        }

        return $this;
    }

    /**
     * @var bool
     */
    private $permissions = false;

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @var bool
     */
    private $permissions_are_permissive = false;

    /**
     * {@inheritdoc}
     */
    public function getPermissionsArePermissive()
    {
        return $this->permissions_are_permissive;
    }

    /**
     * {@inheritdoc}
     */
    public function &permissions($value = true, $permissions_are_permissive = true)
    {
        $this->permissions = (bool) $value;
        $this->permissions_are_permissive = (bool) $permissions_are_permissive;

        if ($this->permissions) {
            $this->removeTrait(
                PermissionsInterface::class,
                [
                    PermissiveImplementation::class,
                    RestrictiveImplementation::class,
                ]
            );

            $this->addTrait(
                PermissionsInterface::class,
                ($this->permissions_are_permissive ? PermissiveImplementation::class : RestrictiveImplementation::class)
            );
        } else {
            $this->removeInterface(PermissionsInterface::class);
        }

        return $this;
    }

    /**
     * @var array
     */
    private $protected_fields = [];

    /**
     * {@inheritdoc}
     */
    public function getProtectedFields()
    {
        return $this->protected_fields;
    }

    /**
     * {@inheritdoc}
     */
    public function &protectFields(...$fields)
    {
        foreach ($fields as $field) {
            if ($field && !in_array($field, $this->protected_fields)) {
                $this->protected_fields[] = $field;
            }
        }

        if (!empty($this->protected_fields)) {
            $this->addTrait(
                ProtectedFieldsInterface::class,
                ProtectedFieldsInterfaceImplementation::class
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &unprotectFields(...$fields)
    {
        foreach ($fields as $field) {
            $index = array_search($field, $this->protected_fields);

            if ($index !== false) {
                unset($this->protected_fields[$index]);
            }
        }

        if (empty($this->protected_fields)) {
            $this->removeTrait(
                ProtectedFieldsInterface::class,
                ProtectedFieldsInterfaceImplementation::class
            );
        } else {
            $this->protected_fields = array_values($this->protected_fields); // Reindex keys
        }

        return $this;
    }

    /**
     * @var FieldInterface[]
     */
    private $fields = [];

    /**
     * {@inheritdoc}
     */
    public function getEntityClassName(): string
    {
        return Inflector::classify(Inflector::singularize($this->getName()));
    }

    /**
     * @var string
     */
    private $base_class_extends;

    /**
     * {@inheritdoc}
     */
    public function getBaseEntityClassExtends(): string
    {
        if (empty($this->base_class_extends)) {
            $this->base_class_extends = Entity::class;
        }

        return $this->base_class_extends;
    }

    /**
     * @var string
     */
    private $base_interface_extends;

    public function getBaseEntityInterfaceExtends(): string
    {
        if (empty($this->base_interface_extends)) {
            $this->base_interface_extends = EntityInterface::class;
        }

        return $this->base_interface_extends;
    }

    public function getManagerClassName(): string
    {
        return Inflector::classify($this->getName());
    }

    public function getCollectionClassName(): string
    {
        return Inflector::classify($this->getName());
    }

    public function getCollectionInterfaceName(): string
    {
        return sprintf('%sInterface', $this->getCollectionClassName());
    }

    /**
     * Set name of a class that base type class should extend.
     *
     * Note: This class needs to descened from Object class of DatabaseObject package
     *
     * @param  string $class_name
     * @return $this
     */
    public function &setBaseClassExtends($class_name)
    {
        if ($class_name
            && class_exists($class_name)
            && (new ReflectionClass($class_name))->isSubclassOf(Entity::class)) {
        } else {
            throw new InvalidArgumentException("Class name '$class_name' is not valid");
        }

        $this->base_class_extends = $class_name;

        return $this;
    }

    /**
     * @var string
     */
    private $expected_dataset_size = FieldInterface::SIZE_NORMAL;

    /**
     * Get expected dataset size.
     *
     * @return string
     */
    public function getExpectedDatasetSize()
    {
        return $this->expected_dataset_size;
    }

    /**
     * Set expected databaset size in following increments: TINY, SMALL, MEDIUM, NORMAL and BIG.
     *
     * @param  string $size
     * @return $this
     */
    public function &expectedDatasetSize($size)
    {
        if (in_array($size, [FieldInterface::SIZE_TINY, FieldInterface::SIZE_SMALL, FieldInterface::SIZE_MEDIUM, FieldInterface::SIZE_NORMAL, FieldInterface::SIZE_BIG])) {
            $this->expected_dataset_size = $size;

            if ($this->id_field instanceof IntegerField && $this->id_field->getSize() != $this->expected_dataset_size) {
                $this->id_field = null; // Reset ID field so it can be recreated with the new size
            }
        } else {
            throw new InvalidArgumentException("Value '$size' is not a valid dataset size");
        }

        return $this;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @var IntegerField
     */
    private $id_field;

    /**
     * Return ID field for this type.
     *
     * @return IntegerField
     */
    public function getIdField()
    {
        if (empty($this->id_field)) {
            $this->id_field = (new IntegerField('id', 0))->unsigned(true)->size($this->getExpectedDatasetSize());
        }

        return $this->id_field;
    }

    /**
     * @var StringField
     */
    private $type_field;

    /**
     * @return StringField
     */
    public function getTypeField()
    {
        if ($this->getPolymorph()) {
            if (empty($this->type_field)) {
                $this->type_field = (new StringField('type', ''))->required();
            }

            return $this->type_field;
        } else {
            throw new BadMethodCallException(__METHOD__ . ' is available only for polymorph types');
        }
    }

    /**
     * @param  FieldInterface[] $fields
     * @return $this
     */
    public function &addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Add a single field to the type.
     *
     * @param  FieldInterface $field
     * @return $this
     */
    public function &addField(FieldInterface $field)
    {
        if (empty($this->fields[$field->getName()])) {
            $this->fields[$field->getName()] = $field;
            $field->onAddedToType($this); // Let the field register indexes, custom behaviour etc
        } else {
            throw new InvalidArgumentException("Field '" . $field->getName() . "' already exists in this type");
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFields()
    {
        $result = [];

        $this->fieldToFlatList($this->getIdField(), $result);

        if ($this->getPolymorph()) {
            $this->fieldToFlatList($this->getTypeField(), $result);
        }

        foreach ($this->getAssociations() as $association) {
            if ($association instanceof InjectFieldsInsterface) {
                foreach ($association->getFields() as $field) {
                    $this->fieldToFlatList($field, $result);
                }
            }
        }

        foreach ($this->getFields() as $field) {
            $this->fieldToFlatList($field, $result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedFields()
    {
        $result = [];

        foreach ($this->getAllFields() as $field) {
            if ($field instanceof GeneratedInterface && $field->isGenerated()) {
                $result[$field->getName()] = $field->getValueCaster();
            } elseif ($field instanceof GeneratedFieldsInterface) {
                $result = array_merge($result, $field->getGeneratedFields());
            }
        }

        return $result;
    }

    /**
     * @param FieldInterface $field
     * @param array          $result
     */
    private function fieldToFlatList(FieldInterface $field, array &$result)
    {
        if ($field instanceof CompositeField) {
            foreach ($field->getFields() as $subfield) {
                $this->fieldToFlatList($subfield, $result);
            }
        } else {
            $result[$field->getName()] = $field;
        }
    }

    /**
     * @var Index[]
     */
    private $indexes = [];

    /**
     * @return IndexInterface[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param  IndexInterface[] $indexes
     * @return $this
     */
    public function &addIndexes(array $indexes)
    {
        foreach ($indexes as $index) {
            $this->addIndex($index);
        }

        return $this;
    }

    /**
     * @param  IndexInterface $index
     * @return $this
     */
    public function &addIndex(IndexInterface $index)
    {
        if (empty($this->indexes[$index->getName()])) {
            $this->indexes[$index->getName()] = $index;
        } else {
            throw new InvalidArgumentException("Index '" . $index->getName() . "' already exists in this type");
        }

        return $this;
    }

    /**
     * Return all indexes.
     *
     * @return IndexInterface[]
     */
    public function getAllIndexes()
    {
        $result = [new Index('id', ['id'], IndexInterface::PRIMARY)];

        if ($this->getPolymorph()) {
            $result[] = new Index('type');
        }

        if (!empty($this->getIndexes())) {
            $result = array_merge($result, $this->getIndexes());
        }

        foreach ($this->getAssociations() as $assosication) {
            if ($assosication instanceof InjectIndexesInsterface) {
                $association_indexes = $assosication->getIndexes();

                if (!empty($association_indexes)) {
                    $result = array_merge($result, $association_indexes);
                }
            }
        }

        return $result;
    }

    /**
     * @var TriggerInterface[]
     */
    private $triggers = [];

    /**
     * @return TriggerInterface[]
     */
    public function getTriggers()
    {
        return $this->triggers;
    }

    /**
     * @param  TriggerInterface[] $triggers
     * @return $this
     */
    public function &addTriggers(array $triggers)
    {
        foreach ($triggers as $trigger) {
            $this->addTrigger($trigger);
        }

        return $this;
    }

    /**
     * @param  TriggerInterface $trigger
     * @return $this
     */
    public function &addTrigger(TriggerInterface $trigger)
    {
        if (empty($this->triggers[$trigger->getName()])) {
            $this->triggers[$trigger->getName()] = $trigger;
        } else {
            throw new InvalidArgumentException("Trigger '" . $trigger->getName() . "' already exists in this type");
        }

        return $this;
    }

    // ---------------------------------------------------
    //  Associations
    // ---------------------------------------------------

    /**
     * @var AssociationInterface[]
     */
    private $associations = [];

    /**
     * @return AssociationInterface[]
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param  AssociationInterface[] $associations
     * @return $this
     */
    public function &addAssociations(array $associations)
    {
        foreach ($associations as $association) {
            $this->addAssociation($association);
        }

        return $this;
    }

    /**
     * @param  AssociationInterface $association
     * @return $this
     */
    public function &addAssociation(AssociationInterface $association)
    {
        if (empty($this->associations[$association->getName()])) {
            $association->setSourceTypeName($this->getName());

            $this->associations[$association->getName()] = $association;
        } else {
            throw new InvalidArgumentException("Association '" . $association->getName() . "' already exists in this type");
        }

        return $this;
    }

    // ---------------------------------------------------
    //  Traits
    // ---------------------------------------------------

    /**
     * Traits.
     *
     * @var array
     */
    private $traits = [];

    /**
     * Return traits.
     *
     * @return array
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Implement an interface or add a trait (or both).
     *
     * @param  string                   $interface
     * @param  string                   $implementation
     * @return $this
     * @throws InvalidArgumentException
     */
    public function &addTrait($interface = null, $implementation = null)
    {
        if (is_array($interface)) {
            foreach ($interface as $k => $v) {
                $this->addTrait($k, $v);
            }
        } else {
            if ($interface || $implementation) {
                if (empty($interface)) {
                    $interface = '--just-paste-trait--';
                }

                if (empty($this->traits[$interface])) {
                    $this->traits[$interface] = [];
                }

                if ($implementation && array_search($implementation, $this->traits[$interface]) === false) {
                    $this->traits[$interface][] = $implementation;
                }
            } else {
                throw new InvalidArgumentException('Interface or implementation are required');
            }
        }

        return $this;
    }

    /**
     * Remove interface and all traits that are added to implement it.
     *
     * @param  string $interface
     * @return $this
     */
    public function &removeInterface($interface)
    {
        if (isset($this->traits[$interface])) {
            unset($this->traits[$interface]);
        }

        return $this;
    }

    /**
     * @param  string       $interface
     * @param  array|string $trait
     * @return $this
     */
    public function &removeTrait($interface, $trait)
    {
        if (isset($this->traits[$interface])) {
            foreach ((array) $trait as $trait_to_remove) {
                $pos = array_search($trait_to_remove, $this->traits[$interface]);

                if ($pos !== false) {
                    unset($this->traits[$interface][$pos]);
                }
            }
        }

        return $this;
    }

    /**
     * Trait conflict resolutions.
     *
     * @var array
     */
    private $trait_tweaks = [];

    /**
     * Return trait tweaks.
     *
     * @return array
     */
    public function getTraitTweaks()
    {
        return $this->trait_tweaks;
    }

    /**
     * Resolve trait conflict.
     *
     * @param  string $tweak
     * @return $this
     */
    public function &addTraitTweak($tweak)
    {
        $this->trait_tweaks[] = $tweak;

        return $this;
    }

    /**
     * @var array|string
     */
    private $order_by = ['id'];

    /**
     * Return how records of this type should be ordered by default.
     *
     * @return string|array
     */
    public function getOrderBy()
    {
        return $this->order_by;
    }

    /**
     * Set how records of this type should be ordered by default.
     *
     * @param  string|array $order_by
     * @return $this
     */
    public function &orderBy($order_by)
    {
        if (empty($order_by)) {
            throw new InvalidArgumentException('Order by value is required');
        } elseif (!is_string($order_by) && !is_array($order_by)) {
            throw new InvalidArgumentException('Order by can be string or array');
        }

        $this->order_by = (array) $order_by;

        return $this;
    }

    /**
     * @var array
     */
    private $serialize = [];

    /**
     * Return a list of additional fields that will be included during object serialization.
     *
     * @return array
     */
    public function getSerialize()
    {
        return $this->serialize;
    }

    /**
     * Set a list of fields that will be included during object serialization.
     *
     * @param  string[] $fields
     * @return $this
     */
    public function &serialize(...$fields)
    {
        if (!empty($fields)) {
            $this->serialize = array_unique(array_merge($this->serialize, $fields));
        }

        return $this;
    }
}
