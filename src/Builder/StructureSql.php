<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
trait StructureSql
{
    /**
     * Return expected structure.sql file path.
     *
     * @return null|string
     */
    public function getStructureSqlPath()
    {
        return $this->getBuildPath() ? "{$this->getBuildPath()}/structure.sql" : null;
    }

    /**
     * Append statement to SQL file.
     *
     * @param string $statement
     * @param string $comment
     */
    public function appendToStructureSql($statement, $comment = '')
    {
        if ($structure_sql_path = $this->getStructureSqlPath()) {
            $current_content = file_get_contents($structure_sql_path);

            if ($current_content) {
                $current_content .= "\n\n\n\n";
            }

            if ($comment) {
                $current_content .= "# $comment\n";
            }

            file_put_contents($structure_sql_path, $current_content . $statement);
        }
    }

    /**
     * @return string
     */
    abstract public function getBuildPath();
}
