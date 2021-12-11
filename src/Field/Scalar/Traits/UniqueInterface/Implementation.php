<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\IndexInterface;

trait Implementation
{
    private bool $is_unique = false;
    private array $uniqueness_context = [];

    public function isUnique(): bool
    {
        return $this->is_unique;
    }

    public function getUniquenessContext(): array
    {
        return $this->uniqueness_context;
    }

    public function unique(string ...$uniqueness_context): static
    {
        $this->is_unique = true;
        $this->uniqueness_context = $uniqueness_context;

        if ($this instanceof AddIndexInterface) {
            $this->addIndex(true, $uniqueness_context, IndexInterface::UNIQUE);
        }

        return $this;
    }
}
