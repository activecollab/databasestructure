<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;

use ActiveCollab\DatabaseStructure\IndexInterface;

trait Implementation
{
    private bool $add_index = false;
    private array $add_index_context = [];
    private string $add_index_type = IndexInterface::INDEX;

    public function getAddIndex(): bool
    {
        return $this->add_index;
    }

    public function getAddIndexContext(): array
    {
        return $this->add_index_context;
    }

    public function getAddIndexType(): string
    {
        return $this->add_index_type;
    }

    public function addIndex(
        bool $add_index = true,
        array $context = [],
        string $type = IndexInterface::INDEX
    ): static
    {
        $this->add_index = $add_index;
        $this->add_index_context = $context;
        $this->add_index_type = $type;

        return $this;
    }
}
