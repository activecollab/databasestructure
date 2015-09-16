<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Unique
{
    /**
     * @var bool
     */
    private $is_unique = false;

    /**
     * @var array
     */
    private $uniquness_context = null;

    /**
     * Value of this column needs to be unique (in the given context)
     *
     * @param  array|string|null $context
     * @return $this
     */
    public function &unique($context = null)
    {
        $this->is_unique = true;

        if ($context) {
            $this->uniquness_context = $context;
        }

        return $this;
    }
}