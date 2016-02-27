<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;
use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface\Implementation as PositionInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class PositionField extends Field
{
    use AddIndexInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $default_value;

    /**
     * @var string
     */
    private $mode = PositionInterface::POSITION_MODE_TAIL;

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
        $this->addIndex($add_index);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return default field value.
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
     * Return position context.
     *
     * @return string[]
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set context in which position is calculated and maintained (usually within context of a foreign key).
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
     * Return position mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Switch position mode to head.
     *
     * @return $this
     */
    public function &head()
    {
        $this->mode = PositionInterface::POSITION_MODE_HEAD;

        return $this;
    }

    /**
     * Switch position mode to tail.
     *
     * @return $this
     */
    public function &tail()
    {
        $this->mode = PositionInterface::POSITION_MODE_TAIL;

        return $this;
    }

    /**
     * Return fields that this field is composed of.
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new IntegerField('position', 0))->unsigned(true)];
    }

    /**
     * Return methods that this field needs to inject in base class.
     *
     * @param string $indent
     * @param array  $result
     */
    public function getBaseClassMethods($indent, array &$result)
    {
        $methods = [];

        $methods[] = '/**';
        $methods[] = ' * Return position mode.';
        $methods[] = ' *';
        $methods[] = ' * There are two modes:';
        $methods[] = ' *';
        $methods[] = ' * * head for new records to be added in front of the other records, or';
        $methods[] = ' * * tail when new records are added after existing records.';
        $methods[] = ' *';
        $methods[] = ' * @return string';
        $methods[] = ' */';
        $methods[] = 'public function getPositionMode()';
        $methods[] = '{';
        $methods[] = '    return ' . var_export($this->mode, true) . ';';
        $methods[] = '}';
        $methods[] = '';
        $methods[] = '/**';
        $methods[] = ' * Return context in which position should be set.';
        $methods[] = ' *';
        $methods[] = ' * @return array';
        $methods[] = ' */';
        $methods[] = 'public function getPositionContext()';
        $methods[] = '{';
        $methods[] = '    return [' . implode(', ', array_map(function ($field_name) {
            return var_export($field_name, true);
        }, $this->getContext())) . '];';
        $methods[] = '}';

        foreach ($methods as $line) {
            if ($line) {
                $result[] = "$indent$line";
            } else {
                $result[] = '';
            }
        }
    }

    /**
     * Method that is called when field is added to a type.
     *
     * @param TypeInterface $type
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        if ($this->getAddIndex()) {
            $type->addIndex(new Index($this->name));
        }

        $type->addTrait(PositionInterface::class, PositionInterfaceImplementation::class);
    }
}
