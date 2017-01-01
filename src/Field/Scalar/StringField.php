<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface\Implementation as LengthInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class StringField extends ScalarFieldWithDefaultValue implements AddIndexInterface, LengthInterface, ModifierInterface
{
    use AddIndexInterfaceImplementation, LengthInterfaceImplementation, ModifierInterfaceImplementation;

    /**
     * @param string      $name
     * @param string|null $default_value
     * @param bool        $add_index
     */
    public function __construct($name, $default_value = null, $add_index = false)
    {
        parent::__construct($name, $default_value);

        $this->addIndex($add_index);
    }

    /**
     * Return PHP native type.
     *
     * @return string
     */
    public function getNativeType(): string
    {
        return 'string';
    }
}
