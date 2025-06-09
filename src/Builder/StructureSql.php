<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

trait StructureSql
{
    public function getStructureSqlPath(): ?string
    {
        return $this->getBuildPath() ? "{$this->getBuildPath()}/SQL/structure.sql" : null;
    }

    public function getInitialDataSqlPath(): ?string
    {
        return $this->getBuildPath() ? "{$this->getBuildPath()}/SQL/initial_data.sql" : null;
    }

    public function appendToStructureSql(string $statement, string $comment = ''): void
    {
        $this->appendToSqlFile($this->getStructureSqlPath(), $statement, $comment);
    }

    public function appendToInitialDataSql(string $statement, string $comment = ''): void
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
    private function appendToSqlFile(
        string $file_path,
        string $statement,
        string $comment,
    ): void
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
    
    abstract public function getBuildPath(): ?string;
}
