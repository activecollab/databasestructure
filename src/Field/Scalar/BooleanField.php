<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use LogicException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class BooleanField extends Field
{
    /**
     * @param string $name
     * @param bool   $default_value
     */
    public function __construct($name, $default_value = false)
    {
        parent::__construct($name, $default_value);
    }

    /**
     * {@inheritdoc}
     */
    public function &unique(...$context)
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
