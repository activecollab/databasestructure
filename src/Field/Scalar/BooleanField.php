<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use LogicException;

class BooleanField extends ScalarFieldWithDefaultValue
{
    /**
     * @param string $name
     * @param bool   $default_value
     */
    public function __construct($name, $default_value = false)
    {
        parent::__construct($name, $default_value);
    }

    public function unique(string ...$context): static
    {
        throw new LogicException('Boolean columns cant be made unique');
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(): string
    {
        return 'bool';
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name): string
    {
        return '(bool) $' . $variable_name;
    }
}
