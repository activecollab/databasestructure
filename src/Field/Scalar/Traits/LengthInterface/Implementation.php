<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface;

use InvalidArgumentException;

trait Implementation
{
    private int $length = 191;

    public function getLength(): int
    {
        return $this->length;
    }

    public function length(int $length): static
    {
        if ($length < $this->getMinLength()) {
            throw new InvalidArgumentException("Min length is {$this->getMinLength()}");
        }

        if ($length > $this->getMaxLength()) {
            throw new InvalidArgumentException("Max length is {$this->getMaxLength()}");
        }

        $this->length = $length;

        return $this;
    }

    protected function getMinLength(): int
    {
        return 1;
    }

    protected function getMaxLength(): int
    {
        return 191;
    }
}
