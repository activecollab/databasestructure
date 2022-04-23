<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

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
     */
    public function addIndexes(array $indexes): static;
    public function addIndex(IndexInterface $index): static;

    /**
     * @return TriggerInterface[]
     */
    public function getTriggers(): array;

    /**
     * @param  TriggerInterface[] $triggers
     */
    public function addTriggers(array $triggers): static;
    public function addTrigger(TriggerInterface $trigger): static;

    /**
     * Return all indexes.
     *
     * @return IndexInterface[]
     */
    public function getAllIndexes(): array;

    /**
     * @return AssociationInterface[]
     */
    public function getAssociations(): array;
    public function addAssociations(array $associations): static;
    public function addAssociation(AssociationInterface $association): static;

    /**
     * Return traits.
     */
    public function getTraits(): array;

    /**
     * Implement an interface or add a trait (or both).
     */
    public function addTrait(
        string $interface = null,
        string $implementation = null
    ): static;

    /**
     * Return trait tweaks.
     */
    public function getTraitTweaks(): array;

    /**
     * Resolve trait conflict.
     */
    public function addTraitTweak(string $tweak): static;

    /**
     * Return how records of this type should be ordered by default.
     */
    public function getOrderBy(): array;

    /**
     * Set how records of this type should be ordered by default.
     */
    public function orderBy(string ...$order_by): static;

    /**
     * Return a list of additional fields that will be included during object serialization.
     */
    public function getSerialize(): array;

    /**
     * Set a list of fields that will be included during object serialization.
     */
    public function serialize(string ...$fields): static;
}
