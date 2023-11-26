<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface ModifierInterface extends FieldTraitInterface
{
    public function getModifier(): ?string;
    public function modifier(string $modifier): static;
}
