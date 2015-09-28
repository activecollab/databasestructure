<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
interface UniqueInterface
{
    /**
     * Return true if this field should be unique
     *
     * @return boolean
     */
    public function isUnique();

    /**
     * Return uniqueness context
     *
     * @return array
     */
    public function getUniquenessContext();

    /**
     * Value of this column needs to be unique (in the given context)
     *
     * @param  string $context
     * @return $this
     */
    public function &unique(...$context);
}