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
use Exception;

abstract class Entity extends BaseEntity implements EntityInterface
{
    /**
     * @return array|AssociatedEntitiesManagerInterface[]
     */
    abstract protected function getAssociatedEntitiesManagers(): array;

    public function &save()
    {
        $is_new = $this->isNew();
        $modifications = $this->getModifications();

        try {
            $this->connection->beginWork();

            parent::save();

            if ($is_new || $this->isModified()) {
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

    public function &delete($bulk = false)
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
