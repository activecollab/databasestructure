<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface\Implementation as GeneratedInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\OnlyOneInterface\Implementation as OnlyOneInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface\Implementation as UniqueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

abstract class ScalarField implements ScalarFieldInterface
{
    use
        GeneratedInterfaceImplementation,
        OnlyOneInterfaceImplementation,
        ProtectSetterInterfaceImplementation,
        RequiredInterfaceImplementation,
        UniqueInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @param  string                   $name
     * @throws InvalidArgumentException
     */
    public function __construct($name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
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
    public function getNativeType(): string
    {
        return 'mixed';
    }

    /**
     * {@inheritdoc}
     */
    public function getDeserializingCode($variable_name): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name): string
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
    public function getShouldBeAddedToModel(): bool
    {
        return $this->should_be_added_to_model;
    }

    /**
     * @param  bool                       $value
     * @return ScalarFieldInterface|$this
     */
    public function &setShouldBeAddedToModel(bool $value): ScalarFieldInterface
    {
        $this->should_be_added_to_model = (bool) $value;

        return $this;
    }
}
