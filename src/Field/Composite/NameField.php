<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndex;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Modifier;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface\Implementation as UniqueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class NameField extends Field implements AddIndexInterface, RequiredInterface, UniqueInterface
{
    use RequiredInterfaceImplementation, UniqueInterfaceImplementation, Modifier, AddIndex;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $default_value;

    /**
     * @param  string                   $name
     * @param  mixed                    $default_value
     * @param  bool|false               $add_index
     * @throws InvalidArgumentException
     */
    public function __construct($name = 'name', $default_value = null, $add_index = false)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
        $this->default_value = $default_value;
        $this->addIndex($add_index);

        $this->modifier('trim');
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
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new StringField($this->getName(), ''))->modifier('trim')];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        if ($this->getAddIndex()) {
            $type->addIndex(new Index($this->name, $this->getAddIndexContext(), $this->getAddIndexType()));
        }
    }
}