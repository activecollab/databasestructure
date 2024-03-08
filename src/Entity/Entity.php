<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Entity;

use ActiveCollab\DatabaseObject\Entity\Entity as BaseEntity;
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\AssociatedEntitiesManagerInterface;
use ActiveCollab\DatabaseStructure\Association\AssociatedEntitiesManager\Base\BaseHasManyAssociatedEntitiesManager;
use Exception;
use LogicException;

abstract class Entity extends BaseEntity implements EntityInterface
{
    /**
     * @return array|AssociatedEntitiesManagerInterface[]
     */
    abstract protected function getAssociatedEntitiesManagers(): array;

    public function getIdsFromAssociationAttributes(string $association_name): array
    {
        $manager = $this->getAssociatedEntitiesManagers()[$association_name] ?? null;

        if (!$manager instanceof AssociatedEntitiesManagerInterface) {
            throw new LogicException("Manager for '$association_name' association not found.");
        }

        if (!$manager instanceof BaseHasManyAssociatedEntitiesManager) {
            throw new LogicException("Association '$association_name' does not handle lists of associated entities.");
        }

        return $manager->getAssociatedEntityIds();
    }

    public function save(): static
    {
        $is_new = $this->isNew();
        $is_modified = $this->isModified();
        $modifications = $is_modified ? $this->getModifications() : [];

        try {
            $this->connection->beginWork();

            parent::save();

            if ($is_new || $is_modified) {
                foreach ($this->getAssociatedEntitiesManagers() as $associated_entities_manager) {
                    if ($is_new) {
                        $associated_entities_manager->afterInsert($this->getId());
                    } else {
                        $associated_entities_manager->afterUpdate($this->getId(), $modifications);
                    }
                }
            }

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }

        return $this;
    }

    public function delete(bool $bulk = false): static
    {
        try {
            $this->connection->beginWork();

            $entity_id = $this->getId();
            foreach ($this->getAssociatedEntitiesManagers() as $associated_entities_manager) {
                $associated_entities_manager->beforeDelete($entity_id);
            }

            parent::delete($bulk);

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }

        return $this;
    }
}
