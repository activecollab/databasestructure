<?php

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
class Index
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param string     $name
     * @param array|null $fields
     */
    public function __construct($name, array $fields = null)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid index name");
        }

        if (is_array($fields) || $fields === null) {
            $fields = empty($fields) ? [$name] : $fields;
        } else {
            throw new InvalidArgumentException("Fields value can be an array of field names or NULL");
        }

        if (empty($fields) || !is_array($fields)) {

        }

        $this->name = $name;
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}