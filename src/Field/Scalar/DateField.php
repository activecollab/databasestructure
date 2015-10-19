<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class DateField extends Field
{
    /**
     * Return PHP native type
     *
     * @return string
     */
    public function getNativeType()
    {
        return '\\ActiveCollab\\DateValue\\DateValueInterface' . ($this->getDefaultValue() === null ? '|null' : '');
    }

    /**
     * Return value casting code
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name)
    {
        return '$this->getDateValueInstanceFrom($' . $variable_name . ')';
    }
}
