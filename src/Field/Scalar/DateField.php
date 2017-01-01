<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DateValue\DateValueInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class DateField extends ScalarFieldWithDefaultValue
{
    /**
     * {@inheritdoc}
     */
    public function getNativeType(): string
    {
        return '\\' . DateValueInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name): string
    {
        return '$this->getDateValueInstanceFrom($' . $variable_name . ')';
    }
}
