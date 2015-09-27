<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface PolymorphInterface
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