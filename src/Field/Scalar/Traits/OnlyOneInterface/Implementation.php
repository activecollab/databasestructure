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
    private bool $allow_only_one = false;
    private mixed $only_one_with_value = null;
    private array $only_one_in_context = [];

    public function allowsOnlyOne(): bool
    {
        return $this->allow_only_one;
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
        string ...$only_one_in_in_context
    ): static
    {
        $this->allow_only_one = true;
        $this->only_one_with_value = $only_with_value;
        $this->only_one_in_context = $only_one_in_in_context;

        if ($this instanceof AddIndexInterface) {
            $this->addIndex(
                true,
                array_merge(
                    [
                        $this->getName(),
                    ],
                    $only_one_in_in_context
                )
            );
        }

        return $this;
    }
}
