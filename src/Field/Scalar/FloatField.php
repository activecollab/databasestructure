<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class FloatField extends NumberField
{
    /**
     * @var int
     */
    private $length = 12;

    /**
     * @var int
     */
    private $scale = 2;

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param  int   $value
     * @return $this
     */
    public function &length($value)
    {
        $this->length = (integer) $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param  int   $value
     * @return $this
     */
    public function &scale($value)
    {
        $this->scale = (integer) $value;

        return $this;
    }

    /**
     * Return value casting code.
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name)
    {
        return '(float) $' . $variable_name;
    }
}
