<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class ForeignKeyField extends Field implements AddIndexInterface, RequiredInterface, SizeInterface
{
    use AddIndexInterfaceImplementation, RequiredInterfaceImplementation, SizeInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string    $name
     * @param bool|true $add_index
     */
    public function __construct($name, $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid foreign key name");
        }

        $this->name = $name;
        $this->addIndex($add_index);
        $this->required();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return fields that this field is composed of.
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        $default_value = $this->isRequired() ? 0 : null;

        return [
            (new IntegerField($this->getName(), $default_value))
                ->unsigned(true)->size($this->getSize())
                ->required($this->isRequired())
        ];
    }
}
