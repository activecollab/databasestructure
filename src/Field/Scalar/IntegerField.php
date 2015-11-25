<?php

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
     * Return PHP native type
     *
     * @return string
     */
    public function getNativeType()
    {
        return 'integer';
    }

    /**
     * Return value casting code
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name)
    {
        return '(integer) $' . $variable_name;
    }
}
