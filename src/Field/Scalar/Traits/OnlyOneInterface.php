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
    public function getOnlyOneWithValue(): mixed;
    public function getOnlyOneInContext(): array;

    public function onlyOne(
        mixed $only_one_with_value,
        string ...$only_one_in_context
    ): static;
}
