<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface;
use ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface\Implementation as IsArchivedInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class IsArchivedField extends Field
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'is_archived';
    }

    /**
     * Return default field value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return false;
    }

    /**
     * @var boolean
     */
    private $cascade = false;

    /**
     * @return bool
     */
    public function getCascade()
    {
        return $this->cascade;
    }

    /**
     * @param  bool|true $value
     * @return $this
     */
    public function cascade($value = true)
    {
        $this->cascade = (boolean) $value;

        return $this;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        $result = [new BooleanField('is_archived', $this->getDefaultValue())];

        if ($this->getCascade()) {
            $result[] = new BooleanField('original_is_archived', false);
        }

        return $result;
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        $type->addTrait(IsArchivedInterface::class, IsArchivedInterfaceImplementation::class);
    }
}