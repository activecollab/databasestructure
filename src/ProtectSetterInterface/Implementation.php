<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\ProtectSetterInterface;

/**
 * @package ActiveCollab\DatabaseStructure\ProtectSetterInterface
 */
trait Implementation
{
    /**
     * @var bool
     */
    private $protect_setter = false;

    /**
     * {@inheritdoc}
     */
    public function getProtectSetter()
    {
        return $this->protect_setter;
    }

    /**
     * {@inheritdoc}
     */
    public function &protectSetter($value = true)
    {
        $this->protect_setter = (boolean) $value;

        return $this;
    }
}
