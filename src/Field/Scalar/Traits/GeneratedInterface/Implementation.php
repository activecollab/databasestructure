<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;

use ActiveCollab\DatabaseStructure\ProtectSetterInterface;

trait Implementation
{
    /**
     * @var bool
     */
    private $is_generated = false;

    /**
     * {@inheritdoc}
     */
    public function isGenerated()
    {
        return $this->is_generated;
    }

    /**
     * {@inheritdoc}
     */
    public function &generated($value = true)
    {
        $this->is_generated = (bool) $value;

        if ($this instanceof ProtectSetterInterface) {
            $this->protectSetter();
        }

        return $this;
    }
}
