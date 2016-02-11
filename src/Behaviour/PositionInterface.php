<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface PositionInterface
{
    const POSITION_MODE_HEAD = 'head';
    const POSITION_MODE_TAIL = 'tail';

    /**
     * Get position value.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position value.
     *
     * @param  int   $value
     * @return $this
     */
    public function &setPosition($value);

    /**
     * Return position mode.
     *
     * There are two modes:
     *
     * * head for new records to be added in front of the other records, or
     * * tail when new records are added after existing records.
     *
     * @return string
     */
    public function getPositionMode();

    /**
     * Return context in which position should be set.
     *
     * @return array
     */
    public function getPositionContext();
}
