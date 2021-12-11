<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

abstract class NumberField extends ScalarFieldWithDefaultValue
{
    private bool $unsigned = false;

    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    public function &unsigned(bool $value = true): static
    {
        $this->unsigned = (bool) $value;

        return $this;
    }
}
