<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface;
use ActiveCollab\DatabaseStructure\Behaviour\IsArchivedInterface\Implementation as IsArchivedInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\TypeInterface;

class IsArchivedField extends CompositeField
{
    public function getName(): string
    {
        return 'is_archived';
    }

    /**
     * Return default field value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return false;
    }

    /**
     * @var bool
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
        $this->cascade = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        $result = [new BooleanField('is_archived', $this->getDefaultValue())];

        if ($this->getCascade()) {
            $result[] = new BooleanField('original_is_archived', false);
        }

        return $result;
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        $type->addTrait(IsArchivedInterface::class, IsArchivedInterfaceImplementation::class)->serialize($this->getName());
    }
}
