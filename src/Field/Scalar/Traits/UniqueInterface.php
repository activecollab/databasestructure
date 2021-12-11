<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface UniqueInterface extends FieldTraitInterface
{
    /**
     * Return true if this field should be unique.
     */
    public function isUnique(): bool;

    /**
     * Return uniqueness context.
     */
    public function getUniquenessContext(): array;

    /**
     * Value of this column needs to be unique (in the given context).
     *
     * @return $this
     */
    public function &unique(string ...$context);
}
