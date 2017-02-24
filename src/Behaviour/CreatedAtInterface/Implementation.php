<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface
 */
trait Implementation
{
    /**
     * Say hello to the parent class.
     */
    public function ActiveCollabDatabaseStructureBehaviourCreatedAtInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function () {
            if (empty($this->getFieldValue('created_at'))) {
                $this->setCreatedAt(new DateTimeValue());
            }
        });
    }

    // ---------------------------------------------------
    //  Expectations
    // ---------------------------------------------------

    /**
     * Register an internal event handler.
     *
     * @param string   $event
     * @param callable $handler
     */
    abstract protected function registerEventHandler($event, callable $handler);

    abstract public function getFieldValue($field, $default = null);

    abstract public function getCreatedAt(): DateTimeValueInterface;

    abstract public function &setCreatedAt(DateTimeValueInterface $value);
}
