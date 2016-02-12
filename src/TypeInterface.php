<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface TypeInterface
{
    /**
     * Return type name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getTableName();

    /**
     * @param  string $table_name
     * @return $this
     */
    public function &setTableName($table_name);

    /**
     * Return name of a class that base type class should extend.
     *
     * @return string
     */
    public function getBaseClassExtends();

    /**
     * Set name of a class that base type class should extend.
     *
     * Note: This class needs to descened from Object class of DatabaseObject package
     *
     * @param  string $class_name
     * @return $this
     */
    public function &setBaseClassExtends($class_name);

    /**
     * @return bool
     */
    public function getPolymorph();

    /**
     * Set this model to be polymorph (type field is added and used to store instance's class name).
     *
     * @param  bool  $value
     * @return $this
     */
    public function &polymorph($value = true);

    /**
     * @return bool
     */
    public function getPermissions();

    /**
     * @return bool
     */
    public function getPermissionsArePermissive();

    /**
     * Add permissions interface to this model.
     *
     * @param  bool  $value
     * @param  bool  $permissions_are_permissive
     * @return $this
     */
    public function &permissions($value = true, $permissions_are_permissive = true);

    /**
     * Return a list of protected fields.
     *
     * @return array
     */
    public function getProtectedFields();

    /**
     * Protect given fields.
     *
     * @param  string[] ...$fields
     * @return $this
     */
    public function &protectFields(...$fields);

    /**
     * Get expected dataset size.
     *
     * @return string
     */
    public function getExpectedDatasetSize();

    /**
     * Set expected databaset size in following increments: TINY, SMALL, MEDIUM, NORMAL and BIG.
     *
     * @param  string $size
     * @return $this
     */
    public function &expectedDatasetSize($size);

    /**
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * Return ID field for this type.
     *
     * @return IntegerField
     */
    public function getIdField();

    /**
     * @param  FieldInterface[] $fields
     * @return $this
     */
    public function &addFields(array $fields);

    /**
     * Add a single field to the type.
     *
     * @param  FieldInterface $field
     * @return $this
     */
    public function &addField(FieldInterface $field);

    /**
     * Return all fields, flatten to one array.
     *
     * @return FieldInterface[]
     */
    public function getAllFields();

    /**
     * @return IndexInterface[]
     */
    public function getIndexes();

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
     * @param  string                   $interface
     * @param  string                   $implementation
     * @return $this
     * @throws InvalidArgumentException
     */
    public function &addTrait($interface = null, $implementation = null);

    /**
     * Return trait tweaks.
     *
     * @return array
     */
    public function getTraitTweaks();

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
     *
     * @return array
     */
    public function getSerialize();

    /**
     * Set a list of fields that will be included during object serialization.
     *
     * @param  string[] ...$fields
     * @return $this
     */
    public function &serialize(...$fields);
}
