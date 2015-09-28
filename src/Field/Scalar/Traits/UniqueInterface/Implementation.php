<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;

use ActiveCollab\DatabaseStructure\Field\AddIndexInterface;
use ActiveCollab\DatabaseStructure\IndexInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Implementation
{
    /**
     * @var bool
     */
    private $is_unique = false;

    /**
     * @var array
     */
    private $uniquness_context = [];

    /**
     * Return true if this field should be unique
     *
     * @return boolean
     */
    public function isUnique()
    {
        return $this->is_unique;
    }

    /**
     * Return uniqueness context
     *
     * @return array
     */
    public function getUniquenessContext()
    {
        return $this->uniquness_context;
    }

    /**
     * Value of this column needs to be unique (in the given context)
     *
     * @param  string $context
     * @return $this
     */
    public function &unique(...$context)
    {
        $this->is_unique = true;
        $this->uniquness_context = $context;

        if ($this instanceof AddIndexInterface) {
            $this->addIndex(true, $context, IndexInterface::UNIQUE);
        }

        return $this;
    }
}