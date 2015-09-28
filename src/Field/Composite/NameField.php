<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface\Implementation as UniqueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class NameField extends Field implements ModifierInterface, RequiredInterface, UniqueInterface, AddIndexInterface
{
    use ModifierInterfaceImplementation, RequiredInterfaceImplementation, UniqueInterfaceImplementation, AddIndexInterfaceImplementation;

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
     * Value of this column needs to be unique (in the given context)
     *
     * @param  string $context
     * @return $this
     */
    public function &unique(...$context)
    {
        $this->is_unique = true;
        $this->uniquness_context = $context;

        if ($this instanceof AddIndexInterface) {
            $this->addIndex(true, $context, IndexInterface::UNIQUE);
        }

        return $this;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        $name_field = (new StringField($this->getName(), ''))->modifier($this->getModifier());

        if ($this->isRequired()) {
            $name_field->required();
        }

        if ($this->isUnique()) {
            $name_field->unique(...$this->getUniquenessContext());
        }

        return [$name_field];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        if ($this->getAddIndex()) {
            $index_fields = [$this->name];

            if (count($this->getAddIndexContext())) {
                $index_fields = array_merge($index_fields, $this->getAddIndexContext());
            }

            $type->addIndex(new Index($this->name, $index_fields, $this->getAddIndexType()));
        }
    }
}