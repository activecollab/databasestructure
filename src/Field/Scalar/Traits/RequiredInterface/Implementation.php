<?php

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
     * Return true if this field is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->is_required;
    }

    /**
     * Value of this column is required
     *
     * @param  boolean $value
     * @return $this
     */
    public function &required($value = true)
    {
        $this->is_required = (boolean) $value;

        return $this;
    }
}