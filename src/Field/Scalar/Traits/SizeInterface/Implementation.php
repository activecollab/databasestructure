<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;

use ActiveCollab\DatabaseStructure\FieldInterface;
use InvalidArgumentException;

trait Implementation
{
    private string $size = FieldInterface::SIZE_NORMAL;

    public function getSize(): string
    {
        return $this->size;
    }

    public function size(string $size): static
    {
        if (!in_array($size, $this->getSupportedSizes())) {
            throw new InvalidArgumentException("Size '$size' is not supported");
        }

        $this->size = $size;

        return $this;
    }

    protected function getSupportedSizes(): array
    {
        return [
            FieldInterface::SIZE_TINY,
            FieldInterface::SIZE_SMALL,
            FieldInterface::SIZE_MEDIUM,
            FieldInterface::SIZE_NORMAL,
            FieldInterface::SIZE_BIG,
        ];
    }
}
