<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface\Implementation as PositionInterfaceImplementation;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class PositionField extends Field
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
     * @var string[]
     */
    private $context = [];

    /**
     * Return position context
     *
     * @return string[]
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set context in which position is calculated and maintained (usually within context of a foreign key)
     *
     * @param  string $fields
     * @return $this
     */
    public function &context(...$fields)
    {
        $this->context = $fields;

        return $this;
    }

    /**
     * Return fields that this field is composed of
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new IntegerField('position', 0))->unsigned(true)];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        if ($this->add_index) {
            $type->addIndex(new Index($this->name));
        }

        $type->addTrait(PositionInterface::class, PositionInterfaceImplementation::class);
    }
}