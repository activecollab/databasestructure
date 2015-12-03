<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class ForeignKeyField extends Field implements RequiredInterface, SizeInterface
{
    use RequiredInterfaceImplementation, SizeInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $add_index;

    /**
     * @param string    $name
     * @param bool|true $add_index
     */
    public function __construct($name, $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid foreign key name");
        }

        $this->name = $name;
        $this->add_index = (boolean) $add_index;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return fields that this field is composed of
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new IntegerField($this->getName()))->unsigned(true)->size($this->getSize())];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        if ($this->add_index) {
            $type->addIndex(new Index($this->getName()));
        }
    }
}
