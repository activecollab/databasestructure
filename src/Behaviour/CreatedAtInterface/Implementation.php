<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;

trait Implementation
{
    public function ActiveCollabDatabaseStructureBehaviourCreatedAtInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function () {
            if (empty($this->getFieldValue('created_at'))) {
                $this->setCreatedAt(new DateTimeValue());
            }
        });
    }

    abstract protected function registerEventHandler(string $event, callable $handler): void;
    abstract public function getFieldValue(string $field, mixed $default = null): mixed;
    abstract public function setCreatedAt(DateTimeValueInterface $value);
}
