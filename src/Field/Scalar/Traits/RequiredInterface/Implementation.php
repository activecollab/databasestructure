<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;

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
        $this->is_required = (boolean) $value;

        return $this;
    }
}
