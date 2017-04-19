<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager;

use ActiveCollab\DatabaseConnection\BatchInsert\BatchInsertInterface;
use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\Base\BaseHasManyAssociatedEntitiesManager;
use ActiveCollab\DatabaseStructure\Entity\EntityInterface;

final class HasAndBelongsToManyAssociatedEntitiesManager extends BaseHasManyAssociatedEntitiesManager
{
    private $connection_table;

    private $source_field_name;

    private $target_field_name;

    public function __construct(
        ConnectionInterface $connection,
        PoolInterface $pool,
        string $connection_table,
        string $source_field_name,
        string $target_field_name,
        string $target_entity_class_name
    )
    {
        parent::__construct($connection, $pool, $target_entity_class_name);

        $this->connection_table = $connection_table;
        $this->source_field_name = $source_field_name;
        $this->target_field_name = $target_field_name;
    }

    /**
     * @param iterable|EntityInterface[]|null $associated_entities
     * @param int                             $source_entity_id
     */
    protected function updateAssociatedEntities(?iterable $associated_entities, int $source_entity_id)
    {
        if ($associated_entities !== null) {
            $ids = [];

            foreach ($associated_entities as $associated_entity) {
                if (!$associated_entity->isLoaded()) {
                    $associated_entity->save();
                }

                $ids[] = $associated_entity->getId();
            }

            $this->updateAssociatedEntityIds($ids, $source_entity_id);
        }
    }

    protected function updateAssociatedEntityIds(?array $associated_entity_ids, int $source_entity_id)
    {
        if ($associated_entity_ids !== null) {
            $batch = $this->getReplaceBatch();

            foreach ($associated_entity_ids as $associated_entity_id) {
                $batch->insert($source_entity_id, $associated_entity_id);
            }

            $batch->done();

            $this->connection->delete(
                $this->connection_table,
                [
                    "$this->source_field_name = ? AND $this->target_field_name NOT IN ?",
                    $source_entity_id,
                    $associated_entity_ids,
                ]
            );
        }
    }

    private function getReplaceBatch(): BatchInsertInterface
    {
        return $this->connection->batchInsert(
            $this->connection_table,
            [
                $this->source_field_name,
                $this->target_field_name,
            ],
            50,
            ConnectionInterface::REPLACE
        );
    }
}
