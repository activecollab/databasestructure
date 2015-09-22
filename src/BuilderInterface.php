<?php

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface BuilderInterface
{
    /**
     * Build type
     *
     * @param \ActiveCollab\DatabaseStructure\Type $type
     */
    public function build(Type $type);

    /**
     * @return \ActiveCollab\DatabaseStructure\Structure
     */
    public function getStructure();

    /**
     * Register an internal event handler
     *
     * @param string   $event
     * @param callable $handler
     */
    public function registerEventHandler($event, callable $handler);
}