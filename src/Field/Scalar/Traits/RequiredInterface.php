<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
interface RequiredInterface
{
    /**
     * Return true if this field is required
     *
     * @return boolean
     */
    public function isRequired();

    /**
     * Value of this column is required
     *
     * @return $this
     */
    public function &required();
}