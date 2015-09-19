<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\String;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Modifier;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Required;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Unique;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Type;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class Name extends Field
{
    use Required, Unique, Modifier;

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
    public function __construct($name = 'name', $default_value = null, $add_index = false)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $this->name = $name;
        $this->default_value = $default_value;
        $this->add_index = $add_index;

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
        return [(new String($this->getName(), ''))->modifier('trim')];
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
    }
}