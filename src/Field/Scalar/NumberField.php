<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
abstract class NumberField extends Field
{
    /**
     * @var boolean
     */
    private $unsigned = false;

    /**
     * Return unsigned
     *
     * @return boolean
     */
    public function getUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * Set unsigned column flag
     *
     * @param  boolean $value
     * @return $this
     */
    public function &unsigned($value = true)
    {
        $this->unsigned = (boolean) $value;

        return $this;
    }
}