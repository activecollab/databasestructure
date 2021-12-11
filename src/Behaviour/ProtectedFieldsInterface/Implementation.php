<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface;

/**
 * @property array $protected_fields
 */
trait Implementation
{
    public function getProtectedFields(): array
    {
        return property_exists($this, 'protected_fields') ? $this->protected_fields : [];
    }
}
