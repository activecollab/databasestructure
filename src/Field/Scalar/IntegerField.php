<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class IntegerField extends NumberField implements RequiredInterface, SizeInterface
{
    use RequiredInterfaceImplementation, SizeInterfaceImplementation;

    /**
     * {@inheritdoc}
     */
    public function getNativeType(): string
    {
        return 'int';
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name): string
    {
        return '(int) $' . $variable_name;
    }
}
