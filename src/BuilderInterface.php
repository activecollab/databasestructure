<?php

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface BuilderInterface
{
    /**
     * Execute prior to type build
     */
    public function preBuild();

    /**
     * Build type
     *
     * @param \ActiveCollab\DatabaseStructure\Type $type
     */
    public function buildType(Type $type);

    /**
     * Execute after types are built
     */
    public function postBuild();

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