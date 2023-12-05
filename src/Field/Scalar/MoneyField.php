<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

class MoneyField extends DecimalField
{
    public function __construct(string $name, float $default_value = null)
    {
        parent::__construct($name, $default_value);

        $this->length(18);
        $this->scale(6);
    }
}
