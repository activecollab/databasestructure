<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use LogicException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Implementation
{
    /**
     * @var bool
     */
    private $is_required = false;

    /**
     * Return true if this field is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->is_required;
    }

    /**
     * Value of this column is required.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &required($value = true)
    {
        if ($this instanceof DefaultValueInterface && $this->getDefaultValue() === null) {
            throw new LogicException("Default value can't NULL empty for required fields.");
        }

        $this->is_required = (bool) $value;

        return $this;
    }
}
