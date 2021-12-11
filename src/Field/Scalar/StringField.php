<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface\Implementation as LengthInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;

class StringField extends ScalarFieldWithDefaultValue implements AddIndexInterface, LengthInterface, ModifierInterface
{
    use AddIndexInterfaceImplementation, LengthInterfaceImplementation, ModifierInterfaceImplementation;

    public function __construct(string $name, string $default_value = null, bool $add_index = false)
    {
        parent::__construct($name, $default_value);

        $this->addIndex($add_index);
    }

    public function getNativeType(): string
    {
        return 'string';
    }
}
