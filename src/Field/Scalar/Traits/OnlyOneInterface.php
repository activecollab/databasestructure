<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface OnlyOneInterface
{
    public function allowsOnlyOne(): bool;
    public function getOnlyOneField(): ?string;
    public function getOnlyOneValue(): mixed;
    public function getOnlyOneContext(): array;

    public function onlyOne(
        string $only_one_field,
        mixed $only_one_value,
        string ...$only_one_context
    ): static;
}
