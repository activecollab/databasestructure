<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

interface IsArchivedInterface
{
    /**
     * Return true if parent object is archived.
     */
    public function getIsArchived(): bool;

    /**
     * Move to archive.
     *
     * @param bool $bulk
     */
    public function moveToArchive($bulk = false);

    /**
     * Restore from archive.
     *
     * @param bool $bulk
     */
    public function restoreFromArchive($bulk = false);
}
