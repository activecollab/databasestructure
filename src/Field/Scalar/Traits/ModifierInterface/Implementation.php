<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;

use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Implementation
{
    /**
     * @var string
     */
    private $modifier;

    /**
     * Return name of the modifier, if set
     *
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param  string $modifier
     * @return $this
     */
    public function &modifier($modifier)
    {
        if (function_exists($modifier)) {
            $this->modifier = $modifier;
        } else {
            throw new InvalidArgumentException("Modifier function '$modifier' does not exist");
        }

        return $this;
    }
}