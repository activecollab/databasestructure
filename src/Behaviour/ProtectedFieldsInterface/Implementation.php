<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface;

/**
 * @property array $protected_fields
 * @package ActiveCollab\DatabaseStructure\Behaviour\ProtectedFieldsInterface
 */
trait Implementation
{
    public function getProtectedFields()
    {
        return property_exists($this, 'protected_fields') ? $this->protected_fields : [];
    }
}
