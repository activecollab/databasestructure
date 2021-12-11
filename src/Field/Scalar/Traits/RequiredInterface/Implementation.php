<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use LogicException;

trait Implementation
{
    /**
     * @var bool
     */
    private $is_required = false;

    /**
     * Return true if this field is required.
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * Value of this column is required.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &required(bool $value = true)
    {
        if ($this instanceof DefaultValueInterface && $this->getDefaultValue() === null) {
            throw new LogicException("Default value can't NULL empty for required fields.");
        }

        $this->is_required = (bool) $value;

        return $this;
    }
}
