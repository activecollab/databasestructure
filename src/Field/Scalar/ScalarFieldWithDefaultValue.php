<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface\Implementation as DefaultValueInterfaceImplementation;
use InvalidArgumentException;

abstract class ScalarFieldWithDefaultValue extends ScalarField implements ScalarFieldWithDefaultValueInterface
{
    use DefaultValueInterfaceImplementation;

    public function __construct(string $name, mixed $default_value = null)
    {
        parent::__construct($name);

        $this->defaultValue($default_value);
    }
}
