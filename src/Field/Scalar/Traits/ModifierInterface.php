<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
interface ModifierInterface
{
    /**
     * Return name of the modifier, if set
     *
     * @return string
     */
    public function getModifier();

    /**
     * @param  string $modifier
     * @return $this
     */
    public function &modifier($modifier);
}