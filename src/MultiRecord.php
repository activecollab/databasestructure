<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

class MultiRecord extends Record implements MultiRecordInterface
{
    private $values;

    public function __construct(string $table_name, array $field_names, array $records_to_add, string $comment = '')
    {
        parent::__construct($table_name, $field_names, $comment);

        $field_names_count = count($field_names);

        foreach ($records_to_add as $k => $record_to_add) {
            if (!is_array($record_to_add)) {
                throw new InvalidArgumentException('Array of arrays expected.');
            }

            if (count($record_to_add) != $field_names_count) {
                throw new InvalidArgumentException("Number of values under key #{$k} does not match the number of fields.");
            }
        }

        $this->values = $records_to_add;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
