<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association\RequiredInterface;

use ActiveCollab\DatabaseStructure\AssociationInterface;

trait Implementation
{
    /**
     * @var bool
     */
    private $is_required = true;

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * Value of this column is required.
     *
     * @param  bool                       $value
     * @return AssociationInterface|$this
     */
    public function &required($value = true): AssociationInterface
    {
        $this->is_required = (bool) $value;

        return $this;
    }
}
