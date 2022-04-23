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
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;

class IntegerField extends NumberField implements RequiredInterface, SizeInterface
{
    use RequiredInterfaceImplementation, SizeInterfaceImplementation;

    public function getNativeType(): string
    {
        return 'int';
    }

    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_INT;
    }

    public function getCastingCode($variable_name): string
    {
        return '(int) $' . $variable_name;
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        $result = match ($this->getSize()) {
            FieldInterface::SIZE_TINY => 'TINYINT',
            FieldInterface::SIZE_SMALL => 'SMALLINT',
            FieldInterface::SIZE_MEDIUM => 'MEDIUMINT',
            FieldInterface::SIZE_BIG => 'BIGINT',
            default => 'INT',
        };

        if ($this->isUnsigned()) {
            $result .= ' UNSIGNED';
        }

        if ($this->getName() == 'id') {
            $result .= ' AUTO_INCREMENT';
        }

        return $result;
    }
}
