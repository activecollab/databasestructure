<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface;
use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface\Implementation as AdditionalPropertiesInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\TextField;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class AdditionalPropertiesField extends Field
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'raw_additional_properties';
    }

    /**
     * Return default field value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        return [
            (new TextField($this->getName(), $this->getDefaultValue()))
                ->size(FieldInterface::SIZE_BIG)->protectSetter(),
        ];
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        $type
            ->addTrait(AdditionalPropertiesInterface::class, AdditionalPropertiesInterfaceImplementation::class)
            ->protectFields($this->getName());
    }
}
