<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Integer;
use ActiveCollab\DatabaseStructure\Type;
use ActiveCollab\DatabaseStructure\Index;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class ForeignKey extends Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $add_index;

    /**
     * @param string    $name
     * @param bool|true $add_index
     */
    public function __construct($name, $add_index = true)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid foreign key name");
        }

        $this->name = $name;
        $this->add_index = (boolean) $add_index;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return fields that this field is composed of
     *
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [(new Integer($this->getName(), 0))->setUnsigned(true)];
    }

    /**
     * Method that is called when field is added to a type
     *
     * @param  Type $type
     */
    public function onAddedToType(Type &$type)
    {
        if ($this->add_index) {
            $type->addIndex(new Index($this->getName()));
        }
    }
}