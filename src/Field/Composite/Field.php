<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\ProtectSetterInterface\Implementation as ProtectSetterInterfaceImplementation;
use ActiveCollab\DatabaseStructure\TypeInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
abstract class Field implements FieldInterface
{
    use ProtectSetterInterfaceImplementation;

    /**
     * Return fields that this field is composed of.
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [];
    }

    /**
     * Return methods that this field needs to inject in base class.
     *
     * @param string $indent
     * @param array  $result
     */
    public function getBaseClassMethods($indent, array &$result)
    {
    }

    /**
     * @param       $indent
     * @param array $result
     */
    public function getValidatorLines($indent, array &$result)
    {
    }

    protected function autoAddIndexWhenAddedToType(): bool
    {
        return true;
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        if ($this instanceof AddIndexInterface && $this->getAddIndex() && $this->autoAddIndexWhenAddedToType()) {
            $type->addIndex(new Index($this->getName(), $this->getAddIndexContext(), $this->getAddIndexType()));
        }
    }
}
