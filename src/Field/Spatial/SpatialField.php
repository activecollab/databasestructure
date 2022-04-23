<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Spatial;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

abstract class SpatialField implements SpatialFieldInterface
{
    use ProtectSetterInterfaceImplementation;

    private string $name;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException(
                sprintf("Value '%s' is not a valid field name.", $name)
            );
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function onAddedToType(TypeInterface $type): void
    {
        if ($this instanceof AddIndexInterface && $this->getAddIndex()) {
            $type->addIndex(
                new Index(
                    $this->getName(),
                    $this->getAddIndexContext(),
                    IndexInterface::SPATIAL
                )
            );
        }
    }

    public function getShouldBeAddedToModel(): bool
    {
        return true;
    }
}
