<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;

class TextField extends ScalarField implements SizeInterface
{
    use SizeInterfaceImplementation;

    public function getNativeType(): string
    {
        return 'string';
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return match ($this->getSize()) {
            FieldInterface::SIZE_TINY => 'TINYTEXT',
            FieldInterface::SIZE_SMALL => 'TEXT',
            FieldInterface::SIZE_MEDIUM => 'MEDIUMTEXT',
            default => 'LONGTEXT',
        };
    }
}
