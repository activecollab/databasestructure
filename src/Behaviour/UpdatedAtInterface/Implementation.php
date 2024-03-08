<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\UpdatedAtInterface;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;

trait Implementation
{
    public function ActiveCollabDatabaseStructureBehaviourUpdatedAtInterfaceImplementation()
    {
        $this->registerEventHandler(
            'on_before_save',
            function () {
                $this->setUpdatedAt(new DateTimeValue());
            },
        );
    }

    abstract protected function registerEventHandler(string $event, callable $handler): void;
    abstract public function getUpdatedAt(): DateTimeValueInterface;
    abstract public function setUpdatedAt(DateTimeValueInterface $value);
}
