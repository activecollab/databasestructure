<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface IsArchivedInterface
{
    /**
     * Return true if parent object is archived
     *
     * @return boolean
     */
    public function getIsArchived();

    /**
     * Move to archive
     *
     * @param boolean $bulk
     */
    public function moveToArchive($bulk = false);

    /**
     * Restore from archive
     *
     * @param boolean $bulk
     */
    public function restoreFromArchive($bulk = false);
}