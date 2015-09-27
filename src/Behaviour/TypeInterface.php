<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface TypeInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param  string $value
     * @return $this
     */
    public function &setType($value);
}