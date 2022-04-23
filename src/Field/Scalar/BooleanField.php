<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use LogicException;

class BooleanField extends ScalarFieldWithDefaultValue
{
    public function __construct(string $name, bool $default_value = false)
    {
        parent::__construct($name, $default_value);
    }

    public function unique(string ...$uniqueness_context): static
    {
        throw new LogicException('Boolean columns cant be made unique');
    }

    public function getNativeType(): string
    {
        return 'bool';
    }

    public function getCastingCode($variable_name): string
    {
        return '(bool) $' . $variable_name;
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return 'TINYINT(1) UNSIGNED';
    }
}
