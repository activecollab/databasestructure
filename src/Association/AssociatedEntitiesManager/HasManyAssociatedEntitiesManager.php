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
        foreach ($this->associated_entities as $associated_entity) {
            $associated_entity
                ->setFieldValue($this->field_name, $entity_id)
                ->save();
        }

        $this->associated_entities = [];

        if (!empty($this->associated_entity_ids)) {
            $this->connection->update(
                $this->table_name,
                [
                    $this->field_name => $entity_id,
                ],
                ['`id` IN ?', $this->associated_entity_ids]
            );
        }

        $this->associated_entity_ids = [];
    }

    public function afterUpdate(int $entity_id, array $modifications)
    {
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
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('A list of instances expected.');
        }

        foreach ($values as $value) {
            if (!$value instanceof $this->entity_class_name) {
                throw new InvalidArgumentException('A list of instances expected.');
            }

            $this->associated_entities[] = $value;
        }

        return $this;
    }

    private $associated_entity_ids = [];

    public function &addAssociatedEntityIds($values)
    {
        if (!is_iterable($values)) {
            throw new InvalidArgumentException('A list of ID-s expected.');
        }

        foreach ($values as $value) {
            if (!is_int($value)) {
                throw new InvalidArgumentException('A list of ID-s expected.');
            }

            $this->associated_entity_ids[] = $value;
        }

        return $this;
    }
}
