<?php

namespace ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface
 * @property ConnectionInterface $connection
 */
trait Implementation
{
    /**
     * Move to archive
     *
     * @param boolean $bulk
     */
    public function moveToArchive($bulk = false)
    {
        $this->connection->transact(function () use ($bulk) {
            $this->triggerEvent('on_before_move_to_archive', [$bulk]);

            if ($bulk && method_exists($this, 'setOriginalIsArchived')) {
                $this->setOriginalIsArchived($this->getIsArchived());
            }

            $this->setIsArchived(true);
            $this->save();

            $this->triggerEvent('on_after_move_to_archive', [$bulk]);
        });
    }

    /**
     * Restore from archive
     *
     * @param boolean $bulk
     */
    public function restoreFromArchive($bulk = false)
    {
        if ($this->getIsArchived()) {
            $this->connection->transact(function () use ($bulk) {
                $this->triggerEvent('on_before_restore_from_archive', [$bulk]);

                if ($bulk && method_exists($this, 'getOriginalIsArchived') && method_exists($this, 'setOriginalIsArchived')) {
                    $this->setIsArchived($this->getOriginalIsArchived());
                    $this->setOriginalIsArchived(false);
                } else {
                    $this->setIsArchived(false);
                }

                $this->save();

                $this->triggerEvent('on_after_restore_from_archive', [$bulk]);
            });
        }
    }

    // ---------------------------------------------------
    //  Expectations
    // ---------------------------------------------------

    /**
     * Return true if parent object is archived
     *
     * @return boolean
     */
    abstract public function getIsArchived();

    /**
     * Set value of is_archived field
     *
     * @param  boolean $value
     * @return boolean
     */
    abstract public function setIsArchived($value);

    /**
     * Save to database
     *
     * @return $this
     */
    abstract public function &save();

    /**
     * Register an internal event handler
     *
     * @param string   $event
     * @param callable $handler
     */
    abstract protected function registerEventHandler($event, callable $handler);

    /**
     * Trigger an internal event
     *
     * @param string     $event
     * @param array|null $event_parameters
     */
    abstract protected function triggerEvent($event, array $event_parameters = null);
}