<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use LogicException;

trait Implementation
{
    private mixed $default_value = null;

    public function getDefaultValue(): mixed
    {
        return $this->default_value;
    }

    public function defaultValue(mixed $value): static
    {
        if ($this instanceof RequiredInterface && $this->isRequired() && $value === null) {
            throw new LogicException("Default value can't NULL empty for required fields.");
        }

        $this->default_value = $value;

        return $this;
    }
}
