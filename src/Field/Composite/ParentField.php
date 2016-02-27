<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseObject\ObjectInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class ParentField extends Field implements AddIndexInterface, RequiredInterface, SizeInterface
{
    use AddIndexInterfaceImplementation, RequiredInterfaceImplementation, SizeInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $relation_name;

    /**
     * @var string
     */
    private $type_field_name;

    /**
     * @param string $name
     * @param bool   $add_index
     */
    public function __construct($name = 'parent_id', $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $name_len = strlen($name);

        if ($name_len <= 3 || substr($name, $name_len - 3) != '_id') {
            throw new InvalidArgumentException("Value '$name' needs to be in parent_id format");
        }

        $this->name = $name;
        $this->relation_name = substr($this->name, 0, $name_len - 3);
        $this->type_field_name = "{$this->relation_name}_type";
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
     * @return string
     */
    public function getRelationName()
    {
        return $this->relation_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $type_field = (new StringField($this->type_field_name));
        $id_field = (new IntegerField($this->name, 0))->size($this->getSize())->unsigned();

        if ($this->isRequired()) {
            $type_field->required();
            $id_field->required();
        }

        return [$type_field, $id_field];
    }

    /**
     * Return methods that this field needs to inject in base class.
     *
     * @param string $indent
     * @param array  $result
     */
    public function getBaseClassMethods($indent, array &$result)
    {
        $type_getter_name = 'get' . Inflector::classify($this->type_field_name);
        $type_setter_name = 'set' . Inflector::classify($this->type_field_name);

        $id_getter_name = 'get' . Inflector::classify($this->name);
        $id_setter_name = 'set' . Inflector::classify($this->name);

        $instance_getter_name = 'get' . Inflector::classify($this->relation_name);
        $instance_setter_name = 'set' . Inflector::classify($this->relation_name);

        $type_hint = '\\' . ObjectInterface::class;

        $methods = [];

        $methods[] = '/**';
        $methods[] = ' * @param  boolean' . str_pad('$use_cache', strlen($type_hint) + 4, ' ', STR_PAD_LEFT);
        $methods[] = ' * @return ' . $type_hint;
        $methods[] = ' */';
        $methods[] = 'public function ' . $instance_getter_name . '($use_cache = true)';
        $methods[] = '{';
        $methods[] = '    if ($id = $this->' . $id_getter_name . '()) {';
        $methods[] = '        return $this->pool->getById($this->' . $type_getter_name . '(), $id, $use_cache);';
        $methods[] = '    } else {';
        $methods[] = '        return null;';
        $methods[] = '    }';
        $methods[] = '}';
        $methods[] = '';

        if ($this->isRequired()) {
            $methods[] = '/**';
            $methods[] = ' * Return context in which position should be set';
            $methods[] = ' *';
            $methods[] = ' * @param  ' . $type_hint . ' $value';
            $methods[] = ' * @return $this';
            $methods[] = ' */';
            $methods[] = 'public function &' . $instance_setter_name . '(\\' . ObjectInterface::class . ' $value)';
            $methods[] = '{';
            $methods[] = '    if ($value instanceof \\' . ObjectInterface::class . ' && $value->isLoaded()) {';
            $methods[] = '        $this->' . $type_setter_name . '(get_class($value));';
            $methods[] = '        $this->' . $id_setter_name . '($value->getId());';
            $methods[] = '    } else {';
            $methods[] = '        throw new \InvalidArgumentException(' . var_export("Instance of '" . ObjectInterface::class . "' expected", true) . ');';
            $methods[] = '    }';
            $methods[] = '';
            $methods[] = '    return $this;';
            $methods[] = '}';
        } else {
            $methods[] = '/**';
            $methods[] = ' * Return context in which position should be set';
            $methods[] = ' *';
            $methods[] = ' * @param  ' . $type_hint . ' $value';
            $methods[] = ' * @return $this';
            $methods[] = ' */';
            $methods[] = 'public function &' . $instance_setter_name . '(\\' . ObjectInterface::class . ' $value = null)';
            $methods[] = '{';
            $methods[] = '    if ($value instanceof \\' . ObjectInterface::class . ') {';
            $methods[] = '        if ($value->isLoaded()) {';
            $methods[] = '            $this->' . $type_setter_name . '(get_class($value));';
            $methods[] = '            $this->' . $id_setter_name . '($value->getId());';
            $methods[] = '        } else {';
            $methods[] = '            throw new \InvalidArgumentException(' . var_export("Instance of '" . ObjectInterface::class . "' expected", true) . ');';
            $methods[] = '        }';
            $methods[] = '    } else {';
            $methods[] = '        $this->' . $type_setter_name . '(null);';
            $methods[] = '        $this->' . $id_setter_name . '(0);';
            $methods[] = '    }';
            $methods[] = '';
            $methods[] = '    return $this;';
            $methods[] = '}';
        }

        foreach ($methods as $line) {
            $result[] = "$indent$line";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        if ($this->getAddIndex()) {
            $type->addIndex(new Index($this->relation_name, [$this->type_field_name, $this->name]));
            $type->addIndex(new Index($this->name));
        }

        $type->serialize($this->type_field_name, $this->name);
    }
}
