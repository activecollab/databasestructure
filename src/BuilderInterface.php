<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

interface BuilderInterface
{
    /**
     * Execute prior to type build.
     */
    public function preBuild();

    /**
     * Build type.
     *
     * @param \ActiveCollab\DatabaseStructure\TypeInterface $type
     */
    public function buildType(TypeInterface $type);

    /**
     * Execute after types are built.
     */
    public function postBuild();

    /**
     * @return \ActiveCollab\DatabaseStructure\Structure
     */
    public function getStructure();

    /**
     * Register an internal event handler.
     *
     * @param string   $event
     * @param callable $handler
     */
    public function registerEventHandler($event, callable $handler);
}
