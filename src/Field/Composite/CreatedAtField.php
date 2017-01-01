<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface;
use ActiveCollab\DatabaseStructure\Behaviour\CreatedAtInterface\Implementation as CreatedAtInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\DateTimeField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class CreatedAtField extends CompositeField implements AddIndexInterface
{
    use AddIndexInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param bool   $add_index = false
     */
    public function __construct($name = 'created_at', $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
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
    public function getFields(): array
    {
        return [new DateTimeField($this->getName())];
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        $type->addTrait(CreatedAtInterface::class, CreatedAtInterfaceImplementation::class)->serialize($this->name);
    }
}
