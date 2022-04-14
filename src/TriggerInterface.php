<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

interface TriggerInterface
{
    const BEFORE = 'before';
    const AFTER = 'after';

    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';

    public function getName(): string;

    /**
     * @return string
     */
    public function getBody();

    /**
     * Return trigger time (before or after).
     *
     * @return string
     */
    public function getTime();

    /**
     * Return trigger event (insert, update or delete).
     *
     * @return string
     */
    public function getEvent();
}
