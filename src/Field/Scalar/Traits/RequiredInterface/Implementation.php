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
    private bool $is_allowing_empty = false;

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    public function isAllowingEmpty(): bool
    {
        return $this->is_allowing_empty;
    }

    public function required(
        bool $is_required = true,
        bool $is_allowing_empty = false,
    ): static
    {
        if ($this instanceof DefaultValueInterface && $this->getDefaultValue() === null) {
            throw new LogicException("Default value can't NULL empty for required fields.");
        }

        $this->is_required = $is_required;
        $this->is_allowing_empty = $is_allowing_empty;

        return $this;
    }
}
