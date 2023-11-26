<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface DefaultValueInterface
{
    public function getDefaultValue(): mixed;
    public function defaultValue(mixed $value): static;
}
