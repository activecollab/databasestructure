<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\TextField;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface;
use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface\Implementation as AdditionalPropertiesInterfaceImplementation;

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
     * Return default field value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return '{}';
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new TextField($this->getName(), $this->getDefaultValue()))->size(FieldInterface::SIZE_BIG)];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        $type->addTrait(AdditionalPropertiesInterface::class, AdditionalPropertiesInterfaceImplementation::class);
    }
}