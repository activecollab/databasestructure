<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Behaviour\ChildInterface\OptionalImplementation as ParentOptionalInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Behaviour\ChildInterface\RequiredImplementation as ParentRequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Behaviour\ParentOptionalInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ParentRequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField as ScalarStringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

class ParentField extends CompositeField implements AddIndexInterface, RequiredInterface, SizeInterface
{
    use AddIndexInterfaceImplementation, RequiredInterfaceImplementation, SizeInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $relation_name;

    /**
     * @var string
     */
    private $type_field_name;

    /**
     * @param string $name
     * @param bool   $add_index
     */
    public function __construct($name = 'parent_id', $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $name_len = strlen($name);

        if ($name_len <= 3 || substr($name, $name_len - 3) != '_id') {
            throw new InvalidArgumentException("Value '$name' needs to be in parent_id format");
        }

        $this->name = $name;
        $this->relation_name = substr($this->name, 0, $name_len - 3);
        $this->type_field_name = "{$this->relation_name}_type";
        $this->addIndex($add_index);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRelationName()
    {
        return $this->relation_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        $type_field = (new ScalarStringField($this->type_field_name));
        $id_field = (new IntegerField($this->name))->size($this->getSize())->unsigned();

        if ($this->isRequired()) {
            $type_field->defaultValue('')->required();
            $id_field->defaultValue(0)->required();
        }

        return [$type_field, $id_field];
    }

    protected function autoAddIndexWhenAddedToType(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        if ($this->isRequired()) {
            $type->addTrait(ParentRequiredInterface::class, ParentRequiredInterfaceImplementation::class);
        } else {
            $type->addTrait(ParentOptionalInterface::class, ParentOptionalInterfaceImplementation::class);
        }

        if ($this->getAddIndex()) {
            $type->addIndex(new Index($this->relation_name, [$this->type_field_name, $this->name]));
            $type->addIndex(new Index($this->name));
        }

        $type->serialize($this->type_field_name, $this->name);
    }
}
