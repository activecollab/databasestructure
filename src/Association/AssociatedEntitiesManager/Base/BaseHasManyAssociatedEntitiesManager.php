<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\Base;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\AssociatedEntitiesManager;
use ActiveCollab\DatabaseStructure\Entity\EntityInterface;
use InvalidArgumentException;

abstract class BaseHasManyAssociatedEntitiesManager extends AssociatedEntitiesManager
{
    /**
     * @var iterable|EntityInterface[]|null
     */
    private $associated_entities;

    /**
     * @var array|int[]|null
     */
    private $associated_entity_ids;

    protected $target_entity_class_name;

    public function __construct(
        ConnectionInterface $connection,
        PoolInterface $pool,
        string $target_entity_class_connection
    )
    {
        parent::__construct($connection, $pool);

        $this->target_entity_class_name = $target_entity_class_connection;
    }

    public function afterInsert(int $entity_id)
    {
        $this->updateAssociatedEntities($this->associated_entities, $entity_id);
        $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id);
        $this->resetAssociatedEntities();
    }

    public function afterUpdate(int $entity_id, array $modifications)
    {
        $this->updateAssociatedEntities($this->associated_entities, $entity_id);
        $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id);
        $this->resetAssociatedEntities();
    }

    abstract protected function updateAssociatedEntities(?iterable $associated_entities, int $entity_id);

    abstract protected function updateAssociatedEntityIds(?array $associated_entity_ids, int $entity_id);

    protected function resetAssociatedEntities()
    {
        $this->associated_entities = null;
        $this->associated_entity_ids = null;
    }

    public function &setAssociatedEntities($values)
    {
        $this->validateListOfEntities($values);

        $this->associated_entities = $values;

        return $this;
    }

    public function &setAssociatedEntityIds($values)
    {
        $this->validateListOfIds($values);

        $this->associated_entity_ids = $values;

        return $this;
    }

    private function validateListOfEntities($values)
    {
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('A list of entities expected.');
        }

        foreach ($values as $value) {
            if (!$value instanceof $this->target_entity_class_name) {
                throw new InvalidArgumentException('A list of entities expected.');
            }
        }
    }

    private function validateListOfIds($values)
    {
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('A list of ID-s expected.');
        }

        foreach ($values as $value) {
            if (!is_int($value)) {
                throw new InvalidArgumentException('A list of ID-s expected.');
            }
        }
    }
}
