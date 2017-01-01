<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface\Implementation as DefaultValueInterfaceImplementation;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
abstract class ScalarFieldWithDefaultValue extends ScalarField implements ScalarFieldWithDefaultValueInterface
{
    use DefaultValueInterfaceImplementation;

    /**
     * @param  string                   $name
     * @param  mixed                    $default_value
     * @throws InvalidArgumentException
     */
    public function __construct($name, $default_value = null)
    {
        parent::__construct($name);

        $this->defaultValue($default_value);
    }
}
