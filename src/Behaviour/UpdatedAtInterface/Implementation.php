<?php

namespace ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface;

use ActiveCollab\DateValue\DateTimeValue;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface
 */
trait Implementation
{
    /**
     * Say hello to the parent class
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
     * Register an internal event handler
     *
     * @param string   $event
     * @param callable $handler
     */
    abstract protected function registerEventHandler($event, callable $handler);

    /**
     * @return \ActiveCollab\DateValue\DateTimeValueInterface|null
     */
    abstract public function getUpdatedAt();

    /**
     * @param  \ActiveCollab\DateValue\DateTimeValueInterface|null $value
     * @return $this
     */
    abstract public function &setUpdatedAt($value);
}
