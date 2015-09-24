<?php

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
     * @param  integer $value
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
     * @param  integer $value
     * @return $this
     */
    public function &scale($value)
    {
        $this->scale = (integer) $value;

        return $this;
    }
}