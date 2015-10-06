<?php

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface TriggerInterface
{
    const BEFORE = 'before';
    const AFTER = 'after';

    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getBody();

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