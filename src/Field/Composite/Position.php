<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Integer;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Type;
use InvalidArgumentException;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface\Implementation as PositionInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class Position extends Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $default_value;

    /**
     * @var boolean
     */
    private $add_index;

    /**
     * @param  string                   $name
     * @param  mixed                    $default_value
     * @param  bool|false               $add_index
     * @throws InvalidArgumentException
     */
    public function __construct($name = 'position', $default_value = 0, $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
        $this->default_value = $default_value;
        $this->add_index = $add_index;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return default field value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Return fields that this field is composed of
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new Integer('position', 0))->setUnsigned(true)];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param  Type $type
     */
    public function onAddedToType(Type &$type)
    {
        if ($this->add_index) {
            $type->addIndex(new Index($this->name));
        }

        $type->addTrait(PositionInterface::class, PositionInterfaceImplementation::class);
    }
}