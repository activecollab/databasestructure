<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use LogicException;

trait Implementation
{
    private bool $is_required = false;

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    public function required(bool $value = true): static
    {
        if ($this instanceof DefaultValueInterface && $this->getDefaultValue() === null) {
            throw new LogicException("Default value can't NULL empty for required fields.");
        }

        $this->is_required = $value;

        return $this;
    }
}
