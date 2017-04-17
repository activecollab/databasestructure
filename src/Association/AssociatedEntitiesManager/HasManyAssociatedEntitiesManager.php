<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Entity\EntityInterface;
use InvalidArgumentException;

class HasManyAssociatedEntitiesManager extends AssociatedEntitiesManager implements HasManyAssociatedEntitiesManagerInterface
{
    private $table_name;

    private $field_name;

    private $target_entity_class_name;

    private $association_is_required;

    public function __construct(
        ConnectionInterface $connection,
        PoolInterface $pool,
        string $table_name,
        string $field_name,
        string $target_entity_class_name,
        bool $association_is_required = false
    )
    {
        parent::__construct($connection, $pool);

        $this->table_name = $table_name;
        $this->field_name = $field_name;
        $this->target_entity_class_name = $target_entity_class_name;
        $this->association_is_required = $association_is_required;
    }

    public function afterInsert(int $entity_id)
    {
        $this->updateAssociatedEntities($entity_id);

        if (!empty($this->associated_entity_ids)) {
            $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id);
        }

        $this->resetAssociatedEntities();
    }

    public function afterUpdate(int $entity_id, array $modifications)
    {
        $this->updateAssociatedEntities($entity_id);

        if ($this->associated_entity_ids !== null) {
            $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id);
        }

        $this->resetAssociatedEntities();
    }

    private function updateAssociatedEntities(int $entity_id)
    {
        if ($this->associated_entities !== null) {
            foreach ($this->associated_entities as $associated_entity) {
                $associated_entity
                    ->setFieldValue($this->field_name, $entity_id)
                    ->save();
            }
        }
    }

    private function updateAssociatedEntityIds(array $associated_entity_ids, int $entity_id)
    {
        $finder = $this->pool->find($this->target_entity_class_name);

        if (empty($associated_entity_ids)) {
            $finder->where("`{$this->field_name}` = ?", $entity_id);
        } else {
            $finder->where("`{$this->field_name}` = ? OR `id` IN ?", $entity_id, $associated_entity_ids);
        }

        /** @var EntityInterface[] $entities_to_update */
        $entities_to_update = $finder->all();

        if ($entities_to_update) {
            foreach ($entities_to_update as $entity_to_update) {
                if (in_array($entity_to_update->getId(), $associated_entity_ids)) {
                    $this->reassignEntity($entity_to_update, $this->field_name, $entity_id);
                } else {
                    $this->releaseEntity($entity_to_update, $this->field_name, $this->association_is_required);
                }
            }
        }
    }

    private function reassignEntity(
        EntityInterface $entity,
        string $field_name,
        int $reassign_to_entity_id
    ): EntityInterface
    {
        return $entity
            ->setFieldValue($field_name, $reassign_to_entity_id)
            ->save();
    }

    private function releaseEntity(
        EntityInterface $entity,
        string $field_name,
        bool $association_is_required
    ): EntityInterface
    {
        if ($association_is_required) {
            throw new \RuntimeException(sprintf(
                "Can't release associated entity #%d because it can only be reassigned, not released.",
                $entity->getId()
            ));
        }

        return $entity
            ->setFieldValue($field_name, null)
            ->save();
    }

    /**
     * @var EntityInterface[]|null
     */
    private $associated_entities;

    public function &setAssociatedEntities($values)
    {
        $this->validateListOfEntities($values);

        $this->associated_entities = $values;

        return $this;
    }

    private $associated_entity_ids;

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

    private function resetAssociatedEntities()
    {
        $this->associated_entities = null;
        $this->associated_entity_ids = null;
    }
}
