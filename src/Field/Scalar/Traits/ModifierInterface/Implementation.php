<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;

use InvalidArgumentException;

trait Implementation
{
    private ?string $modifier = null;

    public function getModifier(): ?string
    {
        return $this->modifier;
    }

    public function modifier(string $modifier): static
    {
        if (!function_exists($modifier)) {
            throw new InvalidArgumentException("Modifier function '$modifier' does not exist");
        }

        $this->modifier = $modifier;

        return $this;
    }
}
