<?php

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
class Type
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @var FieldInterface[]
     */
    private $fields = [];

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param  FieldInterface[] $fields
     * @return $this
     */
    public function &addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Add a single field to the type
     *
     * @param  FieldInterface $field
     * @return $this
     */
    public function &addField(FieldInterface $field)
    {
        if (empty($this->fields[$field->getName()])) {
            $this->fields[$field->getName()] = $field;
            $field->onAddedToType($this); // Let the field register indexes, custom behaviour etc
        } else {
            throw new InvalidArgumentException("Field '" . $field->getName() . "' already exists in this type");
        }

        return $this;
    }

    /**
     * @var Index[]
     */
    private $indexes = [];

    /**
     * @return Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param  Index[] $indexes
     * @return $this
     */
    public function &addIndexes(array $indexes)
    {
        foreach ($indexes as $index) {
            $this->addIndex($index);
        }

        return $this;
    }

    /**
     * @param  Index $index
     * @return $this
     */
    public function &addIndex(Index $index)
    {
        if (empty($this->indexes[$index->getName()])) {
            $this->indexes[$index->getName()] = $index;
        } else {
            throw new InvalidArgumentException("Index '" . $index->getName() . "' already exists in this type");
        }

        return $this;
    }

    /**
     * Traits
     *
     * @var array
     */
    private $traits = [];

    /**
     * Return traits
     *
     * @return array
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Implement an interface or add a trait (or both)
     *
     * @param  string                   $interface
     * @param  string                   $implementation
     * @return $this
     * @throws InvalidArgumentException
     */
    public function &addTrait($interface = null, $implementation = null) {
        if (is_array($interface)) {
            foreach ($interface as $k => $v) {
                $this->addTrait($k, $v);
            }
        } else {
            if ($interface || $implementation) {
                if (empty($interface)) {
                    $interface = '--just-paste-trait--';
                }

                if (empty($this->traits[$interface])) {
                    $this->traits[$interface] = [];
                }

                $this->traits[$interface][] = $implementation;
            } else {
                throw new InvalidArgumentException('Interface or implementation are required');
            }

        }

        return $this;
    }

    /**
     * Trait conflict resolutions
     *
     * @var array
     */
    private $trait_tweaks = [];

    /**
     * Return trait tweaks
     *
     * @return array
     */
    public function getTraitTweaks()
    {
        return $this->trait_tweaks;
    }

    /**
     * Resolve trait conflict
     *
     * @param  string $tweak
     * @return $this
     */
    public function &addTraitTweak($tweak) {
        $this->trait_tweaks[] = $tweak;

        return $this;
    }
}