<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\StringField as ScalarStringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\DefaultValueInterface\Implementation as DefaultValueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface\Implementation as LengthInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\OnlyOneInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\OnlyOneInterface\Implementation as OnlyOneInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface\Implementation as UniqueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

abstract class StringField extends CompositeField implements DefaultValueInterface, RequiredInterface, UniqueInterface, OnlyOneInterface, LengthInterface, ModifierInterface, AddIndexInterface
{
    use DefaultValueInterfaceImplementation, RequiredInterfaceImplementation, UniqueInterfaceImplementation, OnlyOneInterfaceImplementation, LengthInterfaceImplementation, ModifierInterfaceImplementation, AddIndexInterfaceImplementation;

    private string $name;

    public function __construct(
        string $name,
        string $default_value = null,
        bool $add_index = false
    )
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
        $this->defaultValue($default_value);
        $this->addIndex($add_index);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        $scalar_string_field = (new ScalarStringField($this->getName(), $this->getDefaultValue()))->length($this->getLength());

        if ($this->getModifier()) {
            $scalar_string_field->modifier($this->getModifier());
        }

        if ($this->isRequired()) {
            $scalar_string_field->required();
        }

        if ($this->isUnique()) {
            $scalar_string_field->unique(...$this->getUniquenessContext());
        }

        return [$scalar_string_field];
    }

    protected function autoAddIndexWhenAddedToType(): bool
    {
        return false;
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface $type): void
    {
        parent::onAddedToType($type);

        if ($this->getAddIndex()) {
            $index_fields = [$this->name];

            if (count($this->getAddIndexContext())) {
                $index_fields = array_merge($index_fields, $this->getAddIndexContext());
            }

            $type->addIndex(new Index($this->name, $index_fields, $this->getAddIndexType()));
        }
    }
}
