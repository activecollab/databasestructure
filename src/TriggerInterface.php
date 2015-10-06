<?php

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface TriggerInterface
{
    const BEFORE = 'before';
    const AFTER = 'after';

    const INSERT = 'inser';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @return string
     */
    public function getName();

    /**
     * Return trigger time (before or after)
     *
     * @return string
     */
    public function getTime();

    /**
     * Return trigger event (insert, update or delete)
     *
     * @return string
     */
    public function getEvent();
}