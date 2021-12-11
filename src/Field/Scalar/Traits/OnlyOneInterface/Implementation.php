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
    private ?string $only_one_field = null;
    private mixed $only_one_value = null;
    private array $only_one_context = [];

    public function allowsOnlyOne(): bool
    {
        return !empty($this->only_one_field);
    }

    public function getOnlyOneField(): ?string
    {
        return $this->only_one_field;
    }

    public function getOnlyOneValue(): mixed
    {
        return $this->only_one_value;
    }

    public function getOnlyOneContext(): array
    {
        return $this->only_one_context;
    }

    public function onlyOne(
        string $only_one_field,
        mixed $only_one_value,
        string ...$only_one_context
    ): static
    {
        $this->only_one_field = $only_one_field;
        $this->only_one_value = $only_one_value;
        $this->only_one_context = $only_one_context;

        if ($this instanceof AddIndexInterface) {
            $this->addIndex(
                true,
                array_merge(
                    [
                        $only_one_field,
                    ],
                    $only_one_context
                )
            );
        }

        return $this;
    }
}
