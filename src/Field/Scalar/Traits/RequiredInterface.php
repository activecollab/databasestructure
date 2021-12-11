<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface RequiredInterface extends FieldTraitInterface
{
    /**
     * Return true if this field is required.
     */
    public function isRequired(): bool;

    /**
     * Value of this column is required.
     *
     * @return $this
     */
    public function &required(bool $value = true);
}
