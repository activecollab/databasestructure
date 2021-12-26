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

    protected string $target_entity_class_name;

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
        if ($this->entities_are_set) {
            $this->updateAssociatedEntities($this->associated_entities, $entity_id);
        }

        if ($this->entity_ids_are_set) {
            $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id);
        }

        $this->resetAssociatedEntities();
    }

    public function afterUpdate(int $entity_id, array $modifications)
    {
        if ($this->entities_are_set) {
            $this->updateAssociatedEntities($this->associated_entities, $entity_id);
        }

        if ($this->entity_ids_are_set) {
            $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id);
        }

        $this->resetAssociatedEntities();
    }

    abstract protected function updateAssociatedEntities(?iterable $associated_entities, int $source_entity_id);

    abstract protected function updateAssociatedEntityIds(?array $associated_entity_ids, int $source_entity_id);

    protected function resetAssociatedEntities()
    {
        $this->associated_entities = null;
        $this->entities_are_set = false;

        $this->associated_entity_ids = null;
        $this->entity_ids_are_set = false;
    }

    public function getAssociatedEntityIds(): array
    {
        $ids = [];

        if ($this->entities_are_set) {
            foreach ($this->associated_entities as $associated_entity) {
                if (!$associated_entity->isLoaded()) {
                    $associated_entity->save();
                }

                $ids[] = $associated_entity->getId();
            }
        } elseif ($this->entity_ids_are_set) {
            $ids = $this->associated_entity_ids;
        }

        if (!empty($ids)) {
            $ids = array_unique($ids);
            sort($ids);
        }

        return $ids;
    }

    private $entities_are_set = false;

    public function &setAssociatedEntities($values)
    {
        $this->validateListOfEntities($values);

        $this->associated_entities = $values;

        $this->entities_are_set = true;
        $this->entity_ids_are_set = false;
        $this->associated_entity_ids = null;

        return $this;
    }

    private $entity_ids_are_set = false;

    public function &setAssociatedEntityIds($values)
    {
        $this->validateListOfIds($values);

        $this->associated_entity_ids = $values;

        $this->entities_are_set = false;
        $this->entity_ids_are_set = true;
        $this->associated_entities = null;

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
