<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface PositionInterface
{
    const POSITION_MODE_HEAD = 'head';
    const POSITION_MODE_TAIL = 'tail';

    public function getPosition();

    public function &setPosition($value);
}