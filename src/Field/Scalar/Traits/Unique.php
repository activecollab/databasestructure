<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\Field\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;

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
    private $uniquness_context = [];

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