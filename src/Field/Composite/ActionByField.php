<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField as ScalarStringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\User\IdentifiedVisitor;
use ActiveCollab\User\UserInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use InvalidArgumentException;

class ActionByField extends CompositeField implements AddIndexInterface, RequiredInterface
{
    use AddIndexInterfaceImplementation, RequiredInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $user_class_name;

    /**
     * @var string
     */
    private $identified_visitor_class_name;

    /**
     * @param string $name
     * @param string $user_class_name
     * @param string $identified_visitor_class_name
     * @param bool   $add_index
     */
    public function __construct($name, $user_class_name, $identified_visitor_class_name = IdentifiedVisitor::class, $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $name_len = strlen($name);

        if ($name_len <= 6 || substr($name, $name_len - 6) != '_by_id') {
            throw new InvalidArgumentException("Value '$name' needs to be in action_by_id format");
        }

        if (empty($user_class_name)) {
            throw new InvalidArgumentException('User class name is required');
        }

        if (empty($identified_visitor_class_name)) {
            throw new InvalidArgumentException('Anonymous user class name is required');
        }

        $this->name = $name;
        $this->user_class_name = '\\' . ltrim($user_class_name, '\\');
        $this->identified_visitor_class_name = '\\' . ltrim($identified_visitor_class_name, '\\');
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
    public function getUserClassName()
    {
        return $this->user_class_name;
    }

    /**
     * @return string
     */
    public function getIdentifiedvisitorClassName()
    {
        return $this->identified_visitor_class_name;
    }

    public function getFields(): array
    {
        $id_field = (new IntegerField($this->getName(), 0))
            ->unsigned();

        if ($this->isRequired()) {
            $id_field->required();
        }

        return [
            $id_field,
            new ScalarStringField($this->getActionName() . '_by_name'),
            new EmailField($this->getActionName() . '_by_email'),
        ];
    }

    /**
     * Return methods that this field needs to inject in base class.
     *
     * @param string $indent
     * @param array  $result
     */
    public function getBaseClassMethods(string $indent, array &$result): void
    {
        $inflector = $this->getInflector();

        $id_getter_name = 'get' . $inflector->classify($this->name);
        $id_setter_name = 'set' . $inflector->classify($this->name);

        $email_getter_name = 'get' . $inflector->classify($this->getActionName() . '_by_email');
        $email_setter_name = 'set' . $inflector->classify($this->getActionName() . '_by_email');

        $name_getter_name = 'get' . $inflector->classify($this->getActionName() . '_by_name');
        $name_setter_name = 'set' . $inflector->classify($this->getActionName() . '_by_name');

        $instance_getter_name = 'get' . $inflector->classify(substr($this->name, 0, strlen($this->name) - 3));
        $instance_setter_name = 'set' . $inflector->classify(substr($this->name, 0, strlen($this->name) - 3));

        $type_hint = '\\' . UserInterface::class . '|' . $this->identified_visitor_class_name . '|' . $this->user_class_name . '|null';

        $methods = [];

        $methods[] = '/**';
        $methods[] = ' * @param  bool' . str_pad('$use_cache', strlen($type_hint) + 7, ' ', STR_PAD_LEFT);
        $methods[] = ' * @return ' . $type_hint;
        $methods[] = ' */';
        $methods[] = 'public function ' . $instance_getter_name . '($use_cache = true)';
        $methods[] = '{';
        $methods[] = '    if ($id = $this->' . $id_getter_name . '()) {';
        $methods[] = '        return $this->pool->getById(' . var_export($this->user_class_name, true). ', $id, $use_cache);';
        $methods[] = '    } elseif ($email = $this->' . $email_getter_name . '()) {';
        $methods[] = '        return new ' . $this->identified_visitor_class_name . '($this->' . $name_getter_name . '(), $email);';
        $methods[] = '    } else {';
        $methods[] = '        return null;';
        $methods[] = '    }';
        $methods[] = '}';
        $methods[] = '';

        if ($this->isRequired()) {
            $methods[] = '/**';
            $methods[] = ' * Set ' . $this->action_name . '.';
            $methods[] = ' *';
            $methods[] = ' * @param  \\' . UserInterface::class . '|' . $this->user_class_name .' $value';
            $methods[] = ' * @return $this';
            $methods[] = ' */';
            $methods[] = 'public function &' . $instance_setter_name . '(\\' . UserInterface::class . ' $value)';
            $methods[] = '{';
            $methods[] = '    if ($value instanceof ' . $this->user_class_name . ' && $value->isLoaded()) {';
            $methods[] = '        $this->' . $id_setter_name . '($value->getId());';
            $methods[] = '        $this->' . $name_setter_name . '($value->getFullName());';
            $methods[] = '        $this->' . $email_setter_name . '($value->getEmail());';
            $methods[] = '    } else {';
            $methods[] = '        throw new \InvalidArgumentException(' . var_export("Instance of '$this->user_class_name' expected", true) . ');';
            $methods[] = '    }';
            $methods[] = '';
            $methods[] = '    return $this;';
            $methods[] = '}';
        } else {
            $methods[] = '/**';
            $methods[] = ' * Set ' . $this->action_name . '.';
            $methods[] = ' *';
            $methods[] = ' * @param  ' . $type_hint . ' $value';
            $methods[] = ' * @return $this';
            $methods[] = ' */';
            $methods[] = 'public function &' . $instance_setter_name . '(\\' . UserInterface::class . ' $value = null)';
            $methods[] = '{';
            $methods[] = '    if ($value instanceof ' . $this->user_class_name . ') {';
            $methods[] = '        if ($value->isLoaded()) {';
            $methods[] = '            $this->' . $id_setter_name . '($value->getId());';
            $methods[] = '            $this->' . $name_setter_name . '($value->getFullName());';
            $methods[] = '            $this->' . $email_setter_name . '($value->getEmail());';
            $methods[] = '        } else {';
            $methods[] = '            throw new \InvalidArgumentException(' . var_export("Instance of '$this->user_class_name' expected") . ');';
            $methods[] = '        }';
            $methods[] = '    } elseif ($value instanceof ' . $this->identified_visitor_class_name . ') {';
            $methods[] = '        $this->' . $id_setter_name . '(0);';
            $methods[] = '        $this->' . $name_setter_name . '($value->getFullName());';
            $methods[] = '        $this->' . $email_setter_name . '($value->getEmail());';
            $methods[] = '    } else {';
            $methods[] = '        $this->' . $id_setter_name . '(0);';
            $methods[] = '        $this->' . $name_setter_name . '(null);';
            $methods[] = '        $this->' . $email_setter_name . '(null);';
            $methods[] = '    }';
            $methods[] = '';
            $methods[] = '    return $this;';
            $methods[] = '}';
        }

        foreach ($methods as $line) {
            if ($line) {
                $result[] = "$indent$line";
            } else {
                $result[] = '';
            }
        }
    }

    /**
     * Return methods that this field needs to inject in base class.
     *
     * @param string $indent
     * @param array  $result
     */
    public function getBaseInterfaceMethods(string $indent, array &$result): void
    {
        $inflector = $this->getInflector();

        $instance_getter_name = 'get' . $inflector->classify(substr($this->name, 0, strlen($this->name) - 3));
        $instance_setter_name = 'set' . $inflector->classify(substr($this->name, 0, strlen($this->name) - 3));

        $type_hint = '\\' . UserInterface::class . '|' . $this->identified_visitor_class_name . '|' . $this->user_class_name . '|null';

        $methods = [];

        $methods[] = '/**';
        $methods[] = ' * @param  bool' . str_pad('$use_cache', strlen($type_hint) + 7, ' ', STR_PAD_LEFT);
        $methods[] = ' * @return ' . $type_hint;
        $methods[] = ' */';
        $methods[] = 'public function ' . $instance_getter_name . '($use_cache = true);';
        $methods[] = '';

        if ($this->isRequired()) {
            $methods[] = '/**';
            $methods[] = ' * Set ' . $this->action_name . '.';
            $methods[] = ' *';
            $methods[] = ' * @param  \\' . UserInterface::class . '|' . $this->user_class_name .' $value';
            $methods[] = ' * @return $this';
            $methods[] = ' */';
            $methods[] = 'public function &' . $instance_setter_name . '(\\' . UserInterface::class . ' $value);';
        } else {
            $methods[] = '/**';
            $methods[] = ' * Set ' . $this->action_name . '.';
            $methods[] = ' *';
            $methods[] = ' * @param  ' . $type_hint . ' $value';
            $methods[] = ' * @return $this';
            $methods[] = ' */';
            $methods[] = 'public function &' . $instance_setter_name . '(\\' . UserInterface::class . ' $value = null);';
        }

        foreach ($methods as $line) {
            if ($line) {
                $result[] = "$indent$line";
            } else {
                $result[] = '';
            }
        }
    }

    /**
     * @var string
     */
    private $action_name;

    /**
     * Return action name (name without _by_id).
     *
     * @return string
     */
    private function getActionName()
    {
        if (empty($this->action_name)) {
            $this->action_name = substr($this->name, 0, strlen($this->name) - 6);
        }

        return $this->action_name;
    }
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        $type->serialize($this->name);
    }

    private ?Inflector $inflector = null;

    private function getInflector(): Inflector
    {
        if ($this->inflector === null) {
            $this->inflector = InflectorFactory::create()->build();
        }

        return $this->inflector;
    }
}
