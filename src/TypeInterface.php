<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;

interface TypeInterface
{
    public function getName(): string;
    public function getTableName(): string;
    public function setTableName(string $table_name): static;

    public function getEntityClassName(): string;
    public function getEntityInterfaceName(): string;

    /**
     * Return name of a class that base type class should extend.
     */
    public function getBaseEntityClassExtends(): string;

    /**
     * Return name of a class that base type class should extend.
     *
     * @return string
     */
    public function getBaseEntityInterfaceExtends(): string;

    /**
     * Return manager class name.
     */
    public function getManagerClassName(): string;
    public function getManagerInterfaceName(): string;

    /**
     * Return collection class name.
     */
    public function getCollectionClassName(): string;

    /**
     * Return collection class name.
     */
    public function getCollectionInterfaceName(): string;

    /**
     * Set name of a class that base type class should extend.
     *
     * Note: This class needs to descend from Object class of DatabaseObject package
     */
    public function setBaseClassExtends(string $class_name): static;

    public function getPolymorph(): bool;

    /**
     * Set this model to be polymorph (type field is added and used to store instance's class name).
     */
    public function polymorph(bool $value = true): static;
    public function getPermissions(): bool;
    public function getPermissionsArePermissive(): bool;

    /**
     * Add permissions interface to this model.
     */
    public function permissions(bool $value = true, bool $permissions_are_permissive = true): static;

    /**
     * Return a list of protected fields.
     */
    public function getProtectedFields(): array;
    public function protectFields(string ...$fields): static;
    public function unprotectFields(string ...$fields): static;

    /**
     * Get expected dataset size.
     */
    public function getExpectedDatasetSize(): string;

    /**
     * Set expected dataset size in following increments: TINY, SMALL, MEDIUM, NORMAL and BIG.
     */
    public function expectedDatasetSize(string $size): static;

    /**
     * @return FieldInterface[]
     */
    public function getFields(): array;

    /**
     * Return ID field for this type.
     */
    public function getIdField(): FieldInterface;

    /**
     * @param FieldInterface[] $fields
     */
    public function addFields(array $fields): static;

    /**
     * Add a single field to the type.
     */
    public function addField(FieldInterface $field): static;

    /**
     * Return all fields, flatten to one array.
     *
     * @return FieldInterface[]
     */
    public function getAllFields(): array;

    /**
     * Return an array of generated fields. Key is the field name, value is the caster.
     */
    public function getGeneratedFields(): array;

    /**
     * @return IndexInterface[]
     */
    public function getIndexes(): array;

    /**
     * @param  IndexInterface[] $indexes
     * @return $this
     */
    public function &addIndexes(array $indexes);

    /**
     * @param  IndexInterface $index
     * @return $this
     */
    public function &addIndex(IndexInterface $index);

    /**
     * @return TriggerInterface[]
     */
    public function getTriggers();

    /**
     * @param  TriggerInterface[] $triggers
     * @return $this
     */
    public function &addTriggers(array $triggers);

    /**
     * @param  TriggerInterface $trigger
     * @return $this
     */
    public function &addTrigger(TriggerInterface $trigger);

    /**
     * Return all indexes.
     *
     * @return IndexInterface[]
     */
    public function getAllIndexes();

    /**
     * @return AssociationInterface[]
     */
    public function getAssociations();

    /**
     * @param  AssociationInterface[] $associations
     * @return $this
     */
    public function &addAssociations(array $associations);

    /**
     * @param  AssociationInterface $association
     * @return $this
     */
    public function &addAssociation(AssociationInterface $association);

    /**
     * Return traits.
     *
     * @return array
     */
    public function getTraits();

    /**
     * Implement an interface or add a trait (or both).
     *
     * @return $this
     */
    public function &addTrait(string $interface = null, string $implementation = null);

    /**
     * Return trait tweaks.
     */
    public function getTraitTweaks(): array;

    /**
     * Resolve trait conflict.
     *
     * @param  string $tweak
     * @return $this
     */
    public function &addTraitTweak($tweak);

    /**
     * Return how records of this type should be ordered by default.
     *
     * @return string|array
     */
    public function getOrderBy();

    /**
     * Set how records of this type should be ordered by default.
     *
     * @param  string|array $order_by
     * @return $this
     */
    public function &orderBy($order_by);

    /**
     * Return a list of additional fields that will be included during object serialization.
     */
    public function getSerialize(): array;

    /**
     * Set a list of fields that will be included during object serialization.
     *
     * @return $this
     */
    public function &serialize(string ...$fields);
}
