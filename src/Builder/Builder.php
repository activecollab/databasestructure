<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\BuilderInterface;
use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
abstract class Builder implements BuilderInterface
{
    /**
     * @var StructureInterface
     */
    private $structure;

    /**
     * Construct a new builder instance
     *
     * @param StructureInterface $structure
     */
    public function __construct(StructureInterface $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return StructureInterface
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * Registered event handlers
     *
     * @var array
     */
    private $event_handlers = [];

    /**
     * Execute prior to type build
     */
    public function preBuild()
    {
    }

    /**
     * Build type
     *
     * @param \ActiveCollab\DatabaseStructure\TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
    }

    /**
     * Execute after types are built
     */
    public function postBuild()
    {
    }

    /**
     * Register an internal event handler
     *
     * @param string   $event
     * @param callable $handler
     */
    public function registerEventHandler($event, callable $handler)
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
     * Trigger an internal event
     *
     * @param string     $event
     * @param array|null $event_parameters
     */
    protected function triggerEvent($event, array $event_parameters = null)
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