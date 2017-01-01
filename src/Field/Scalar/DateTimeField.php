<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DateValue\DateTimeValueInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class DateTimeField extends ScalarFieldWithDefaultValue
{
    /**
     * {@inheritdoc}
     */
    public function getNativeType(): string
    {
        return '\\' . DateTimeValueInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name): string
    {
        return '$this->getDateTimeValueInstanceFrom($' . $variable_name . ')';
    }
}
