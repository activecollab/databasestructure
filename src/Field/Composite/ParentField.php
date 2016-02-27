<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class ParentField extends Field implements AddIndexInterface, RequiredInterface, SizeInterface
{
    use AddIndexInterfaceImplementation, RequiredInterfaceImplementation, SizeInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    private $type_field_name;

    private $id_field_name;

    /**
     * @param string $name
     * @param bool   $add_index
     */
    public function __construct($name = 'parent', $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
        $this->type_field_name = "{$this->name}_type";
        $this->id_field_name = "{$this->name}_id";
        $this->addIndex($add_index);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $type_field = (new StringField($this->type_field_name));
        $id_field = (new IntegerField($this->id_field_name, 0))->size($this->getSize())->unsigned();

        if ($this->isRequired()) {
            $type_field->required();
            $id_field->required();
        }

        return [$id_field, $type_field];
    }

    /**
     * {@inheritdoc}
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        if ($this->getAddIndex()) {
            $type->addIndex(new Index($this->name));
        }

        $type->serialize($this->type_field_name, $this->id_field_name);
    }
}
