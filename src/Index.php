<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

class Index implements IndexInterface
{
    private string $name;
    private array $fields;
    private string $index_type;

    public function __construct(
        string $name,
        array $fields = null,
        string $index_type = self::INDEX,
    )
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid index name");
        }

        $fields = empty($fields) ? [$name] : $fields;

        if (!in_array($index_type, self::INDEX_TYPES)) {
            throw new InvalidArgumentException("Value '$index_type' is not a valid index type");
        }

        $this->name = $name;
        $this->fields = $fields;
        $this->index_type = $index_type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getIndexType(): string
    {
        return $this->index_type;
    }
}
