<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface
 */
trait Implementation
{
    /**
     * Say hello to the parent class.
     */
    public function ActiveCollabDatabaseStructureBehaviourUpdatedAtInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function () {
            $this->setUpdatedAt(new DateTimeValue());
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

    abstract public function getUpdatedAt(): DateTimeValueInterface;

    abstract public function &setUpdatedAt(DateTimeValueInterface $value);
}
