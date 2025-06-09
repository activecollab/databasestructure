<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Builder\SqlElement\SqlElementInterface;

trait StructureSql
{
    private $sql_elemenets = [];

    public function getStructureSqlPath(): ?string
    {
        return $this->getBuildPath() ? "{$this->getBuildPath()}/SQL/structure.sql" : null;
    }

    public function getInitialDataSqlPath(): ?string
    {
        return $this->getBuildPath() ? "{$this->getBuildPath()}/SQL/initial_data.sql" : null;
    }

    public function appendToStructureSql(
        ?SqlElementInterface $sql_element,
        string $statement,
        string $comment = '',
    ): void
    {
        if ($sql_element) {
            if (empty($this->sql_elemenets[$sql_element->getType()])) {
                $this->sql_elemenets[$sql_element->getType()] = [];
            }

            if (in_array($sql_element->getName(), $this->sql_elemenets[$sql_element->getType()])) {
                return;
            }
        }

        $this->appendToSqlFile($this->getStructureSqlPath(), $statement, $comment);

        if ($sql_element) {
            $this->sql_elemenets[$sql_element->getType()][] = $sql_element->getName();
        }
    }

    public function appendToInitialDataSql(string $statement, string $comment = ''): void
    {
        $this->appendToSqlFile($this->getInitialDataSqlPath(), $statement, $comment);
    }

    private function appendToSqlFile(
        ?string $file_path,
        string $statement,
        string $comment,
    ): void
    {
        if (!$file_path) {
            return;
        }

        $current_content = file_get_contents($file_path);

        if ($current_content) {
            $current_content .= "\n\n\n\n";
        }

        if ($comment) {
            $current_content .= "# $comment\n";
        }

        file_put_contents($file_path, $current_content . $statement);
    }
    
    abstract public function getBuildPath(): ?string;
}
