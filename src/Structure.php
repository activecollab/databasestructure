<?php

namespace ActiveCollab\DatabaseStructure;

use ActiveCollab\DatabaseConnection\Connection;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure
 */
abstract class Structure
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->configure();
    }

    /**
     * Configure types, fields and associations
     */
    abstract protected function configure();

    /**
     * @var Type[]
     */
    private $types = [];

    /**
     * @param  string $type_name
     * @return Type
     */
    protected function &addType($type_name)
    {
        if (empty($this->types[$type_name])) {
            $this->types[$type_name] = new Type($type_name);

            return $this->types[$type_name];
        } else {
            throw new InvalidArgumentException("Type '$type_name' already added");
        }
    }
}