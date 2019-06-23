<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

abstract class NumberField extends ScalarFieldWithDefaultValue
{
    /**
     * @var bool
     */
    private $unsigned = false;

    /**
     * Return unsigned.
     *
     * @return bool
     */
    public function isUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * Set unsigned column flag.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &unsigned($value = true)
    {
        $this->unsigned = (bool) $value;

        return $this;
    }
}
