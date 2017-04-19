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
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\Base\BaseHasManyAssociatedEntitiesManager;
use ActiveCollab\DatabaseStructure\Entity\EntityInterface;
use RuntimeException;

final class HasManyAssociatedEntitiesManager extends BaseHasManyAssociatedEntitiesManager implements HasManyAssociatedEntitiesManagerInterface
{
    private $table_name;

    private $field_name;

    private $association_is_required;

    public function __construct(
        ConnectionInterface $connection,
        PoolInterface $pool,
        string $connection_table,
        string $field_name,
        string $target_entity_class_name,
        bool $association_is_required
    )
    {
        parent::__construct($connection, $pool, $target_entity_class_name);

        $this->table_name = $connection_table;
        $this->field_name = $field_name;
        $this->association_is_required = $association_is_required;
    }

    /**
     * @param iterable|EntityInterface[]|null $associated_entities
     * @param int                             $source_entity_id
     */
    protected function updateAssociatedEntities(?iterable $associated_entities, int $source_entity_id)
    {
        if ($associated_entities !== null) {
            $reassigned_entity_ids = [];

            if (!empty($associated_entities)) {
                foreach ($associated_entities as $associated_entity) {
                    $this->reassignEntity($associated_entity, $source_entity_id);
                    $reassigned_entity_ids[] = $associated_entity->getId();
                }
            }

            $finder = $this->pool->find($this->target_entity_class_name);

            if (empty($reassigned_entity_ids)) {
                $finder->where("`$this->field_name` = ?", $source_entity_id);
            } else {
                $finder->where("`$this->field_name` = ? AND `id` NOT IN ?", $source_entity_id, $reassigned_entity_ids);
            }

            /** @var EntityInterface[] $entities_to_release */
            $entities_to_release = $finder->all();

            if ($entities_to_release) {
                foreach ($entities_to_release as $entity_to_release) {
                    $this->releaseEntity($entity_to_release, $this->association_is_required);
                }
            }
        }
    }

    protected function updateAssociatedEntityIds(?array $associated_entity_ids, int $source_entity_id)
    {
        if ($associated_entity_ids !== null) {
            $finder = $this->pool->find($this->target_entity_class_name);

            if (empty($associated_entity_ids)) {
                $finder->where("`{$this->field_name}` = ?", $source_entity_id);
            } else {
                $finder->where("`{$this->field_name}` = ? OR `id` IN ?", $source_entity_id, $associated_entity_ids);
            }

            /** @var EntityInterface[] $entities_to_update */
            $entities_to_update = $finder->all();

            if ($entities_to_update) {
                foreach ($entities_to_update as $entity_to_update) {
                    if (in_array($entity_to_update->getId(), $associated_entity_ids)) {
                        $this->reassignEntity($entity_to_update, $source_entity_id);
                    } else {
                        $this->releaseEntity($entity_to_update, $this->association_is_required);
                    }
                }
            }
        }
    }

    private function reassignEntity(
        EntityInterface $entity,
        int $reassign_to_entity_id
    ): void
    {
        $entity
            ->setFieldValue($this->field_name, $reassign_to_entity_id)
            ->save();
    }

    private function releaseEntity(
        EntityInterface $entity,
        bool $association_is_required
    ): void
    {
        if ($association_is_required) {
            throw new RuntimeException(sprintf(
                "Can't release associated entity #%d because it can only be reassigned, not released.",
                $entity->getId()
            ));
        }

        $entity
            ->setFieldValue($this->field_name, null)
            ->save();
    }
}
