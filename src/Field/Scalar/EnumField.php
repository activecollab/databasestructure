<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class EnumField extends ScalarFieldWithDefaultValue
{
    /**
     * @var string[]
     */
    private $possibilities = [];

    /**
     * @return string[]
     */
    public function getPossibilities()
    {
        return $this->possibilities;
    }

    /**
     * Set a list of possible values.
     *
     * @param  string[] $possibilities
     * @return $this
     */
    public function &possibilities(...$possibilities)
    {
        if ($this->getDefaultValue() !== null && !in_array($this->getDefaultValue(), $possibilities)) {
            throw new InvalidArgumentException('Default value ' . var_export($this->getDefaultValue(), true) . ' needs to be in the list of possibilities');
        }

        $this->possibilities = $possibilities;

        return $this;
    }
}
