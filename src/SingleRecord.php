<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

class SingleRecord extends Record implements SingleRecordInterface
{
    private $values;

    public function __construct(string $table_name, array $record_to_add, string $comment = '')
    {
        parent::__construct($table_name, array_keys($record_to_add), $comment);

        $this->values = array_values($record_to_add);
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
