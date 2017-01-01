<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface\Implementation as DefaultValueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface\Implementation as UniqueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
abstract class Field implements FieldInterface, DefaultValueInterface, RequiredInterface, UniqueInterface
{
    use ProtectSetterInterfaceImplementation, DefaultValueInterfaceImplementation, RequiredInterfaceImplementation, UniqueInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

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
        $this->defaultValue($default_value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return PHP native type.
     *
     * @return string
     */
    public function getNativeType()
    {
        return 'mixed';
    }

    /**
     * Return de-serialized value, on get field value.
     *
     * This method should be unsed only for fields that store serialized data, like JSON or serialized PHP values.
     *
     * @param  string $variable_name
     * @return string
     */
    public function getDeserializingCode($variable_name)
    {
        return '';
    }

    /**
     * Return value casting code, that is called when value is set for a field.
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name)
    {
        return '(string) $' . $variable_name;
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        if ($this instanceof AddIndexInterface && $this->getAddIndex()) {
            $type->addIndex(new Index($this->getName(), $this->getAddIndexContext(), $this->getAddIndexType()));
        }
    }

    /**
     * @var bool
     */
    private $should_be_added_to_model = true;

    /**
     * Return true if this field should be part of the model, or does it do its work in background.
     *
     * @return bool
     */
    public function getShouldBeAddedToModel()
    {
        return $this->should_be_added_to_model;
    }

    /**
     * @param  bool  $value
     * @return $this
     */
    public function &setShouldBeAddedToModel($value)
    {
        $this->should_be_added_to_model = (bool) $value;

        return $this;
    }
}
