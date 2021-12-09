<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

use InvalidArgumentException;

class Index implements IndexInterface
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
     * @var string
     */
    private $index_type;

    /**
     * @param string     $name
     * @param array|null $fields
     * @param string     $index_type
     */
    public function __construct($name, array $fields = null, $index_type = self::INDEX)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid index name");
        }

        if (is_array($fields) || $fields === null) {
            $fields = empty($fields) ? [$name] : $fields;
        } else {
            throw new InvalidArgumentException('Fields value can be an array of field names or NULL');
        }

        if (!in_array($index_type, [self::PRIMARY, self::UNIQUE, self::INDEX, self::FULLTEXT])) {
            throw new InvalidArgumentException("Value '$index_type' is not a valid index type");
        }

        $this->name = $name;
        $this->fields = $fields;
        $this->index_type = $index_type;
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

    /**
     * @return string
     */
    public function getIndexType()
    {
        return $this->index_type;
    }
}
