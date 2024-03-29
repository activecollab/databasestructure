<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\PolymorphInterface;

trait Implementation
{
    /**
     * Say hello to the parent class.
     */
    public function ActiveCollabDatabaseStructureBehaviourPolymorphInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function () {
            if (!$this->getType()) {
                $this->setType(get_class($this));
            }
        });
    }

    /**
     * @return string
     */
    abstract public function getType(): string;

    /**
     * @param  string $value
     * @return $this
     */
    abstract public function setType(string $value);
    abstract protected function registerEventHandler(string $event, callable $handler): void;
}
