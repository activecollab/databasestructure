<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use InvalidArgumentException;

class EnumField extends ScalarFieldWithDefaultValue
{
    private array $possibilities = [];

    public function getPossibilities(): array
    {
        return $this->possibilities;
    }

    public function &possibilities(string ...$possibilities): static
    {
        if ($this->getDefaultValue() !== null && !in_array($this->getDefaultValue(), $possibilities)) {
            throw new InvalidArgumentException('Default value ' . var_export($this->getDefaultValue(), true) . ' needs to be in the list of possibilities');
        }

        $this->possibilities = $possibilities;

        return $this;
    }

    public function getNativeType(): string
    {
        return 'string';
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return sprintf(
            'ENUM(%s)',
            implode(
                ',',
                array_map(
                    function ($possibility) use ($connection) {
                        return $connection->escapeValue($possibility);
                    },
                    $this->getPossibilities()
                )
            )
        );
    }
}
