<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

abstract class Record implements RecordInterface
{
    private $table_name;

    private $fields;

    private $comment;

    public function __construct(string $table_name, array $field_names, string $comment)
    {
        if (empty($table_name)) {
            throw new InvalidArgumentException('Table name is required.');
        }

        if (empty($field_names)) {
            throw new InvalidArgumentException('List of field names is required.');
        }

        $this->table_name = $table_name;
        $this->fields = $field_names;
        $this->comment = $comment;
    }

    public function getTableName(): string
    {
        return $this->table_name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
