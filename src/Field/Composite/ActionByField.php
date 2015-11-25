<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\IntegerField;
use ActiveCollab\DatabaseStructure\Field\Scalar\StringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface\Implementation as AddIndexInterfaceImplementation;
use InvalidArgumentException;
use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class ActionByField extends Field
{
    use AddIndexInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string  $name
     * @param boolean $add_index = false
     */
    public function __construct($name, $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        $name_len = strlen($name);

        if ($name_len <= 6 || substr($name, $name_len - 6) != '_by_id') {
            throw new InvalidArgumentException("Value '$name' needs to be in action_by_id format");
        }

        $this->name = $name;
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
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [
            (new IntegerField($this->getName(), 0))->unsigned(),
            new StringField($this->getActionName() . '_by_name'),
            new EmailField($this->getActionName() . '_by_email'),
        ];
    }

    /**
     * @var string
     */
    private $action_name;

    /**
     * Return action name (name without _by_id)
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
}
