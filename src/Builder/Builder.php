<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\Builder\BuilderInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

abstract class Builder implements BuilderInterface
{
    private $structure;

    public function __construct(StructureInterface $structure)
    {
        $this->structure = $structure;
    }

    public function getStructure(): StructureInterface
    {
        return $this->structure;
    }

    /**
     * Registered event handlers.
     *
     * @var array
     */
    private $event_handlers = [];

    public function preBuild(): void
    {
    }

    public function buildType(TypeInterface $type)
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

        if (is_callable($handler)) {
            if (empty($this->event_handlers[$event])) {
                $this->event_handlers[$event] = [];
            }

            $this->event_handlers[$event][] = $handler;
        } else {
            throw new InvalidArgumentException('Handler not callable');
        }
    }

    /**
     * Trigger an internal event.
     *
     * @param string $event
     * @param array  $event_parameters
     */
    protected function triggerEvent($event, array $event_parameters = [])
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
