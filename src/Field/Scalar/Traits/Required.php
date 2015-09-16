<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Required
{
    /**
     * @var bool
     */
    private $is_required = false;

    /**
     * Value of this column is required
     *
     * @return $this
     */
    public function &required()
    {
        $this->is_required = true;

        return $this;
    }
}