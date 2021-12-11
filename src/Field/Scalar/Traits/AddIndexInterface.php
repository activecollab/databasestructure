<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\IndexInterface;

interface AddIndexInterface extends FieldTraitInterface
{
    public function getAddIndex(): bool;
    public function getAddIndexContext(): array;
    public function getAddIndexType(): string;
    public function addIndex(
        bool $add_index = true,
        array $context = [],
        string $type = IndexInterface::INDEX
    ): static;
}
