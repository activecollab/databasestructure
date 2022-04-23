<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DateValue\DateTimeValueInterface;

class DateTimeField extends ScalarField
{
    public function getNativeType(): string
    {
        return '\\' . DateTimeValueInterface::class;
    }

    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_DATETIME;
    }

    public function getCastingCode($variable_name): string
    {
        return '$this->getDateTimeValueInstanceFrom($' . $variable_name . ')';
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return 'DATETIME';
    }
}
