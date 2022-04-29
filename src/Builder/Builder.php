<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\BuilderInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

abstract class Builder implements BuilderInterface
{
    private StructureInterface $structure;

    public function __construct(StructureInterface $structure)
    {
        $this->structure = $structure;
    }

    public function getStructure(): StructureInterface
    {
        return $this->structure;
    }

    private array $event_handlers = [];

    public function preBuild(): void
    {
    }

    public function buildType(TypeInterface $type): void
    {
    }

    public function postBuild(): void
    {
    }

    public function registerEventHandler(string $event, callable $handler): void
    {
        if (empty($event)) {
            throw new InvalidArgumentException('Event name is required');
        }

        if (!is_callable($handler)) {
            throw new InvalidArgumentException('Handler not callable');
        }

        if (empty($this->event_handlers[$event])) {
            $this->event_handlers[$event] = [];
        }

        $this->event_handlers[$event][] = $handler;
    }

    protected function triggerEvent(string $event, array $event_parameters = []): void
    {
        if (isset($this->event_handlers[$event])) {
            if (empty($event_parameters)) {
                $event_parameters = [];
            }

            foreach ($this->event_handlers[$event] as $handler) {
                call_user_func_array($handler, $event_parameters);
            }
        }
    }
}
