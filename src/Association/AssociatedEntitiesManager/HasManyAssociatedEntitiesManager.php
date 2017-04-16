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

class HasManyAssociatedEntitiesManager extends AssociatedEntitiesManager
{
    private $table_name;

    private $field_name;

    private $entity_class_name;

    public function __construct(
        ConnectionInterface $connection,
        PoolInterface $pool,
        string $table_name,
        string $field_name,
        string $entity_class_name
    )
    {
        parent::__construct($connection, $pool);

        $this->table_name = $table_name;
        $this->field_name = $field_name;
        $this->entity_class_name = $entity_class_name;
    }

    public function afterInsert(int $entity_id)
    {
        $this->updateAssociatedEntities($entity_id);

        if (!empty($this->associated_entity_ids)) {
            $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id, true);
        }

        $this->resetAssociatedEntities();
    }

    public function afterUpdate(int $entity_id, array $modifications)
    {
        $this->updateAssociatedEntities($entity_id);

        if ($this->associated_entity_ids !== null) {
            if (empty($this->associated_entity_ids)) {
                $this->nullifyAssociatedEntities($entity_id);
            } else {
                $this->updateAssociatedEntityIds($this->associated_entity_ids, $entity_id, false);
            }
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

    private function updateAssociatedEntityIds(array $associated_entity_ids, int $entity_id, bool $is_new)
    {
        $this->connection->update(
            $this->table_name,
            [
                $this->field_name => $entity_id,
            ],
            ['`id` IN ?', $associated_entity_ids]
        );

        if (!$is_new) {
            $this->connection->update(
                $this->table_name,
                [
                    $this->field_name => null,
                ],
                ["`{$this->field_name}` = ? AND `id` NOT IN ?", $entity_id, $associated_entity_ids]
            );
        }
    }

    private function nullifyAssociatedEntities(int $entity_id)
    {
        $this->connection->update(
            $this->table_name,
            [
                $this->field_name => null,
            ],
            ["`{$this->field_name}` = ?", $entity_id]
        );
    }

    public function beforeDelete(int $entity_id)
    {
    }

    /**
     * @var EntityInterface[]
     */
    private $associated_entities = [];

    public function &addAssociatedEntities($values)
    {
        $this->validateListOfEntities($values);

        if ($this->associated_entities === null) {
            $this->associated_entities = $values;
        } else {
            $this->associated_entities = array_merge(
                $this->associated_entities,
                $values
            );
        }

        return $this;
    }

    private $associated_entity_ids = null;

    public function &addAssociatedEntityIds($values)
    {
        $this->validateListOfIds($values);

        if ($this->associated_entity_ids === null) {
            $this->associated_entity_ids = $values;
        } else {
            $this->associated_entity_ids = array_merge(
                $this->associated_entity_ids,
                $values
            );

            $this->associated_entity_ids = array_unique($this->associated_entity_ids);
        }

        return $this;
    }

    private function validateListOfEntities($values)
    {
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('A list of entities expected.');
        }

        foreach ($values as $value) {
            if (!$value instanceof $this->entity_class_name) {
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
