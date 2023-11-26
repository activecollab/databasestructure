<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
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

    private string $name;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException(
                sprintf("Value '%s' is not a valid field name.", $name)
            );
        }

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNativeType(): string
    {
        return 'mixed';
    }

    public function getDeserializingCode($variable_name): string
    {
        return '';
    }

    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_STRING;
    }

    public function getCastingCode(string $variable_name): string
    {
        return '(string) $' . $variable_name;
    }

    abstract public function getSqlTypeDefinition(ConnectionInterface $connection): string;

    public function getSqlReadStatement(string $table_name): string
    {
        return sprintf('`%s`.`%s`', $table_name, $this->getName());
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface $type): void
    {
        if ($this instanceof AddIndexInterface && $this->getAddIndex()) {
            $type->addIndex(
                new Index(
                    $this->getName(),
                    $this->getAddIndexContext(),
                    $this->getAddIndexType()
                ),
            );
        }
    }

    private bool $should_be_added_to_model = true;

    /**
     * Return true if this field should be part of the model, or does it do its work in background.
     */
    public function getShouldBeAddedToModel(): bool
    {
        return $this->should_be_added_to_model;
    }

    public function &setShouldBeAddedToModel(bool $value): ScalarFieldInterface
    {
        $this->should_be_added_to_model = $value;

        return $this;
    }
}
