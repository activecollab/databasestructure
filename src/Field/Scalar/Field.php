<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Required;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Unique;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Type;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
abstract class Field implements FieldInterface
{
    use Required, Unique;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $default_value;

    /**
     * @param  string                   $name
     * @param  mixed                    $default_value
     * @throws InvalidArgumentException
     */
    public function __construct($name, $default_value = null)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
        $this->default_value = $default_value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return default field value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Return PHP native type
     *
     * @return string
     */
    public function getNativeType()
    {
        return 'mixed';
    }

    /**
     * Return value casting code
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name)
    {
        return '(string) $' . $variable_name;
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param  Type $type
     */
    public function onAddedToType(Type &$type)
    {
    }

    /**
     * @var boolean
     */
    private $should_be_added_to_model = true;

    /**
     * Return true if this field should be part of the model, or does it do its work in background
     *
     * @return boolean
     */
    public function getShouldBeAddedToModel()
    {
        return $this->should_be_added_to_model;
    }

    /**
     * @param  boolean $value
     * @return $this
     */
    public function &setShouldBeAddedToModel($value)
    {
        $this->should_be_added_to_model = (boolean) $value;

        return $this;
    }

    /**
     * @return string
     */
//    abstract public function getCreateFieldStatement();
}