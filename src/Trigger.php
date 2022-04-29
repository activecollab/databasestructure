<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

abstract class Trigger implements TriggerInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $body;

    /**
     * @param string $name
     * @param string $body
     */
    public function __construct($name, $body)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Trigger name is require');
        }

        if (empty($body)) {
            throw new InvalidArgumentException('Trigger body is require');
        }

        $this->name = $name;
        $this->body = $body;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
