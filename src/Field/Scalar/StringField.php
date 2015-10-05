<?php
namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class StringField extends Field implements ModifierInterface
{
    use ModifierInterfaceImplementation;

    /**
     * Return PHP native type
     *
     * @return string
     */
    public function getNativeType()
    {
        return 'string';
    }

    /**
     * @var integer
     */
    private $length = 191;

    /**
     * Return field length (default is 191)
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set field lenght to 1 .. 191 value
     *
     * @param  integer $value
     * @return $this
     */
    public function &length($value)
    {
        $value = (integer) $value;

        if ($value > 0 && $value <= 191) {
            $this->length = $value;
        } else {
            throw new InvalidArgumentException("Lenght can be a value between 1 and 191, $value given");
        }

        return $this;
    }
}
