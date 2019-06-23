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
        return $this->getBuildPath() ? "{$this->getBuildPath()}/SQL/structure.sql" : null;
    }

    /**
     * Return expected initial_data.sql path.
     *
     * @return null|string
     */
    public function getInitialDataSqlPath()
    {
        return $this->getBuildPath() ? "{$this->getBuildPath()}/SQL/initial_data.sql" : null;
    }

    /**
     * Append statement to structure.sql file.
     *
     * @param string $statement
     * @param string $comment
     */
    public function appendToStructureSql($statement, $comment = '')
    {
        $this->appendToSqlFile($this->getStructureSqlPath(), $statement, $comment);
    }

    /**
     * Append statement to initial_data.sql file.
     *
     * @param string $statement
     * @param string $comment
     */
    public function appendToInitialDataSql($statement, $comment = '')
    {
        $this->appendToSqlFile($this->getInitialDataSqlPath(), $statement, $comment);
    }

    /**
     * Append statement to a SQL file.
     *
     * @param string $file_path
     * @param string $statement
     * @param string $comment
     */
    private function appendToSqlFile($file_path, $statement, $comment)
    {
        if ($file_path) {
            $current_content = file_get_contents($file_path);

            if ($current_content) {
                $current_content .= "\n\n\n\n";
            }

            if ($comment) {
                $current_content .= "# $comment\n";
            }

            file_put_contents($file_path, $current_content . $statement);
        }
    }

    /**
     * @return string
     */
    abstract public function getBuildPath();
}
