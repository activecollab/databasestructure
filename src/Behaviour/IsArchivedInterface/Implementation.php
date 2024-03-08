<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

/**
 * @property ConnectionInterface $connection
 */
trait Implementation
{
    /**
     * Say hello to the parent class.
     */
    public function ActiveCollabDatabaseStructureBehaviourIsArchivedInterfaceImplementation()
    {
        $this->registerEventHandler(
            'on_json_serialize',
            function (array &$result) {
                $result['is_archived'] = $this->getIsArchived();
            }
        );
    }

    /**
     * Move to archive.
     *
     * @param bool $bulk
     */
    public function moveToArchive($bulk = false)
    {
        $this->connection->transact(
            function () use ($bulk) {
                $this->triggerEvent('on_before_move_to_archive', [$bulk]);

                if ($bulk && method_exists($this, 'setOriginalIsArchived')) {
                    $this->setOriginalIsArchived($this->getIsArchived());
                }

                $this->setIsArchived(true);
                $this->save();

                $this->triggerEvent('on_after_move_to_archive', [$bulk]);
            }
        );
    }

    /**
     * Restore from archive.
     *
     * @param bool $bulk
     */
    public function restoreFromArchive($bulk = false)
    {
        if (!$this->getIsArchived()) {
            return;
        }

        $this->connection->transact(
            function () use ($bulk) {
                $this->triggerEvent('on_before_restore_from_archive', [$bulk]);

                if ($bulk && method_exists($this, 'getOriginalIsArchived') && method_exists($this, 'setOriginalIsArchived')) {
                    $this->setIsArchived($this->getOriginalIsArchived());
                    $this->setOriginalIsArchived(false);
                } else {
                    $this->setIsArchived(false);
                }

                $this->save();

                $this->triggerEvent('on_after_restore_from_archive', [$bulk]);
            }
        );
    }

    // ---------------------------------------------------
    //  Expectations
    // ---------------------------------------------------

    abstract public function getIsArchived(): bool;

    /**
     * Set value of is_archived field.
     *
     * @return bool
     */
    abstract public function setIsArchived(bool $value);

    abstract public function save(): static;
    abstract protected function registerEventHandler(string $event, callable $handler): void;
    abstract protected function triggerEvent(string $event, array $event_parameters = []): void;
}
