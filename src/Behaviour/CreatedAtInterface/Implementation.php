<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface;

use ActiveCollab\DateValue\DateTimeValue;

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
            if (empty($this->getCreatedAt())) {
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

    /**
     * Return value of created_at field.
     *
     * @return \ActiveCollab\DateValue\DateTimeValueInterface|null
     */
    abstract public function getCreatedAt();

    /**
     * @param  \ActiveCollab\DateValue\DateTimeValueInterface|null $value
     * @return $this
     */
    abstract public function &setCreatedAt($value);
}
