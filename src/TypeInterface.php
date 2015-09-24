<?php

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface TypeInterface
{
    /**
     * Return type name
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
     * Return name of a class that base type class should extend
     *
     * @return string
     */
    public function getBaseClassExtends();

    /**
     * Set name of a class that base type class should extend
     *
     * Note: This class needs to descened from Object class of DatabaseObject package
     *
     * @param  string $class_name
     * @return $this
     */
    public function &setBaseClassExtends($class_name);

    /**
     * Get expected dataset size
     *
     * @return string
     */
    public function getExpectedDatasetSize();

    /**
     * Set expected databaset size in following increments: TINY, SMALL, MEDIUM, NORMAL and BIG
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
     * Return ID field for this type
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
     * Add a single field to the type
     *
     * @param  FieldInterface $field
     * @return $this
     */
    public function &addField(FieldInterface $field);

    /**
     * Return all fields, flatten to one array
     *
     * @return array
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
     * Return all indexes
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
     * Return traits
     *
     * @return array
     */
    public function getTraits();

    /**
     * Implement an interface or add a trait (or both)
     *
     * @param  string                   $interface
     * @param  string                   $implementation
     * @return $this
     * @throws InvalidArgumentException
     */
    public function &addTrait($interface = null, $implementation = null);

    /**
     * Return trait tweaks
     *
     * @return array
     */
    public function getTraitTweaks();

    /**
     * Resolve trait conflict
     *
     * @param  string $tweak
     * @return $this
     */
    public function &addTraitTweak($tweak);
}