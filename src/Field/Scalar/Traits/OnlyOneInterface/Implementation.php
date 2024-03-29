<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\OnlyOneInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;

trait Implementation
{
    private bool $is_only_one = false;
    private mixed $only_one_with_value = null;
    private array $only_one_in_context = [];

    public function isOnlyOne(): bool
    {
        return $this->is_only_one;
    }

    public function getOnlyOneWithValue(): mixed
    {
        return $this->only_one_with_value;
    }

    public function getOnlyOneInContext(): array
    {
        return $this->only_one_in_context;
    }

    public function onlyOne(
        mixed $only_with_value,
        string ...$only_one_in_context
    ): static
    {
        $this->is_only_one = true;
        $this->only_one_with_value = $only_with_value;
        $this->only_one_in_context = $only_one_in_context;

        if ($this instanceof AddIndexInterface && !$this->getAddIndex()) {
            $this->addIndex(true, $only_one_in_context);
        }

        return $this;
    }
}
