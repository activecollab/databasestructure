<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;

use ActiveCollab\DatabaseStructure\ProtectSetterInterface;

trait Implementation
{
    private bool $is_generated = false;

    public function isGenerated(): bool
    {
        return $this->is_generated;
    }

    public function generated(bool $value = true): static
    {
        $this->is_generated = (bool) $value;

        if ($this instanceof ProtectSetterInterface) {
            $this->protectSetter();
        }

        return $this;
    }
}
