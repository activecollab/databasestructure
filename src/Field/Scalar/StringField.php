<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface\Implementation as LengthInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class StringField extends Field implements LengthInterface, ModifierInterface
{
    use LengthInterfaceImplementation, ModifierInterfaceImplementation;

    /**
     * Return PHP native type.
     *
     * @return string
     */
    public function getNativeType()
    {
        return 'string';
    }
}
