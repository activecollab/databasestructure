<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

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
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use InvalidArgumentException;
use ReflectionClass;

class Type implements TypeInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private ?string $table_name = null;

    public function getTableName(): string
    {
        if (empty($this->table_name)) {
            $this->table_name = $this->getName();
        }

        return $this->table_name;
    }

    public function setTableName(string $table_name): static
    {
        if (empty($table_name)) {
            throw new InvalidArgumentException("Value '$table_name' is not a valid table name");
        }

        $this->table_name = $table_name;

        return $this;
    }

    private bool $polymorph = false;

    public function getPolymorph(): bool
    {
        return $this->polymorph;
    }

    public function polymorph(bool $value = true): static
    {
        $this->polymorph = $value;

        if ($this->polymorph) {
            $this->addTrait(PolymorphInterface::class, PolymorphInterfaceImplementation::class);
        }

        return $this;
    }

    private bool $permissions = false;

    public function getPermissions(): bool
    {
        return $this->permissions;
    }

    private bool $permissions_are_permissive = false;

    public function getPermissionsArePermissive(): bool
    {
        return $this->permissions_are_permissive;
    }

    public function permissions(bool $value = true, bool $permissions_are_permissive = true): static
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

    private array $protected_fields = [];

    public function getProtectedFields(): array
    {
        return $this->protected_fields;
    }

    public function protectFields(string ...$fields): static
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

    public function unprotectFields(string ...$fields): static
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

    public function getEntityClassName(): string
    {
        $inflector = $this->getInflector();

        return $inflector->classify($inflector->singularize($this->getName()));
    }

    public function getEntityInterfaceName(): string
    {
        return sprintf('%sInterface', $this->getEntityClassName());
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
        return $this->getInflector()->classify($this->getName());
    }

    public function getManagerInterfaceName(): string
    {
        return sprintf('%sInterface', $this->getManagerClassName());
    }

    public function getCollectionClassName(): string
    {
        return $this->getInflector()->classify($this->getName());
    }

    public function getCollectionInterfaceName(): string
    {
        return sprintf('%sInterface', $this->getCollectionClassName());
    }

    public function setBaseClassExtends(string $class_name): static
    {
        if (!$this->isEntitySubclass($class_name)) {
            throw new InvalidArgumentException("Class name '$class_name' is not valid");
        }

        $this->base_class_extends = $class_name;

        return $this;
    }

    private function isEntitySubclass(string $class_name): bool
    {
        return $class_name
            && class_exists($class_name)
            && (new ReflectionClass($class_name))->isSubclassOf(Entity::class);
    }

    private string $expected_dataset_size = FieldInterface::SIZE_NORMAL;

    /**
     * Get expected dataset size.
     */
    public function getExpectedDatasetSize(): string
    {
        return $this->expected_dataset_size;
    }

    public function expectedDatasetSize(string $size): static
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
    public function getFields(): array
    {
        return $this->fields;
    }

    private ?FieldInterface $id_field = null;

    /**
     * Return ID field for this type.
     */
    public function getIdField(): IntegerField
    {
        if (empty($this->id_field)) {
            $this->id_field = (new IntegerField('id', 0))
                ->unsigned(true)
                ->size($this->getExpectedDatasetSize());
        }

        return $this->id_field;
    }

    private ?StringField $type_field = null;

    public function getTypeField(): StringField
    {
        if (!$this->getPolymorph()) {
            throw new BadMethodCallException(__METHOD__ . ' is available only for polymorph types');
        }

        if (empty($this->type_field)) {
            $this->type_field = (new StringField('type', ''))->required();
        }

        return $this->type_field;
    }

    public function addFields(FieldInterface ...$fields): static
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Add a single field to the type.
     */
    public function addField(FieldInterface $field): static
    {
        if (!empty($this->fields[$field->getName()])) {
            throw new InvalidArgumentException(
                sprintf("Field '%s' already exists in this type.", $field->getName())
            );
        }

        $this->fields[$field->getName()] = $field;
        $field->onAddedToType($this); // Let the field register indexes, custom behaviour etc

        return $this;
    }

    public function getAllFields(): array
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

    public function getGeneratedFields(): array
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
    private array $indexes = [];

    /**
     * @return IndexInterface[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    public function addIndexes(IndexInterface ...$indexes): static
    {
        foreach ($indexes as $index) {
            $this->addIndex($index);
        }

        return $this;
    }

    public function addIndex(IndexInterface $index): static
    {
        if (empty($this->indexes[$index->getName()])) {
            $this->indexes[$index->getName()] = $index;
        } else {
            throw new InvalidArgumentException("Index '" . $index->getName() . "' already exists in this type");
        }

        return $this;
    }

    public function getAllIndexes(): array
    {
        $result = [
            new Index('id', ['id'], IndexInterface::PRIMARY),
        ];

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

    private array $triggers = [];

    public function getTriggers(): array
    {
        return $this->triggers;
    }

    public function addTriggers(TriggerInterface ...$triggers): static
    {
        foreach ($triggers as $trigger) {
            $this->addTrigger($trigger);
        }

        return $this;
    }

    public function addTrigger(TriggerInterface $trigger): static
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

    private array $associations = [];

    public function getAssociations(): array
    {
        return $this->associations;
    }

    public function addAssociations(AssociationInterface ...$associations): static
    {
        foreach ($associations as $association) {
            $this->addAssociation($association);
        }

        return $this;
    }

    public function addAssociation(AssociationInterface $association): static
    {
        if (!empty($this->associations[$association->getName()])) {
            throw new InvalidArgumentException(
                sprintf("Association '%s' already exists in this type.", $association->getName())
            );
        }

        $association->setSourceTypeName($this->getName());
        $this->associations[$association->getName()] = $association;

        return $this;
    }

    // ---------------------------------------------------
    //  Traits
    // ---------------------------------------------------

    private array $traits = [];

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function addTrait(
        string $interface = null,
        string $implementation = null
    ): static
    {
        if (empty($interface) && empty($implementation)) {
            throw new InvalidArgumentException('Interface or implementation are required');
        }

        if (empty($interface)) {
            $interface = '--just-paste-trait--';
        }

        if (empty($this->traits[$interface])) {
            $this->traits[$interface] = [];
        }

        if ($implementation && !in_array($implementation, $this->traits[$interface])) {
            $this->traits[$interface][] = $implementation;
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

    private array $trait_tweaks = [];

    public function getTraitTweaks(): array
    {
        return $this->trait_tweaks;
    }

    public function addTraitTweak(string $tweak): static
    {
        $this->trait_tweaks[] = $tweak;

        return $this;
    }

    private array $order_by = [
        'id',
    ];

    public function getOrderBy(): array
    {
        return $this->order_by;
    }

    public function orderBy(string ...$order_by): static
    {
        if (empty($order_by)) {
            throw new InvalidArgumentException('Order by value is required');
        }

        $this->order_by = $order_by;

        return $this;
    }

    private array $serialize = [];

    /**
     * Return a list of additional fields that will be included during object serialization.
     */
    public function getSerialize(): array
    {
        return $this->serialize;
    }

    public function serialize(string ...$fields): static
    {
        if (!empty($fields)) {
            $this->serialize = array_unique(array_merge($this->serialize, $fields));
        }

        return $this;
    }

    private ?Inflector $inflector = null;

    private function getInflector(): Inflector
    {
        if ($this->inflector === null) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
