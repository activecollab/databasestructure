<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface\Implementation as UniqueInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\ModifierInterface\Implementation as ModifierInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class EmailField extends Field implements RequiredInterface, UniqueInterface, ModifierInterface, AddIndexInterface
{
    use RequiredInterfaceImplementation, UniqueInterfaceImplementation, ModifierInterfaceImplementation, AddIndexInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $default_value;

    /**
     * @param string      $name
     * @param string|null $default_value
     * @param boolean     $add_index = false
     */
    public function __construct($name, $default_value = null, $add_index = false)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid foreign key name");
        }

        $this->name = $name;
        $this->default_value = $default_value;
        $this->addIndex($add_index);

        if ($this->default_value !== null) {
            $this->modifier('trim');
        }
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
     * @return string|null
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
        $email_field = new StringField($this->getName(), $this->getDefaultValue());

        if ($this->getDefaultValue() !== null) {
            $email_field->modifier('trim');
        }

        if ($this->isRequired()) {
            $email_field->required();
        }

        if ($this->isUnique()) {
            $email_field->unique(...$this->getUniquenessContext());
        }

        return [$email_field];
    }

    /**
     * Prepare validator lines
     *
     * @param string $indent
     * @param array  $result
     */
    public function getValidatorLines($indent, array &$result)
    {
        $result[] = $indent . '$validator->email(' . var_export($this->getName(), true) . ', ' . ($this->getDefaultValue() === null ? 'true' : 'false') . ');';
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