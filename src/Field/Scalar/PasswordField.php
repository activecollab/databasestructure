<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

class PasswordField extends ScalarField
{
    public function __construct($name = 'password')
    {
        parent::__construct($name);

        $this->required(true);
    }

    public function getNativeType(): string
    {
        return 'string';
    }
}
