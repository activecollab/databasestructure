<?php

namespace ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\PositionInterface
 * @property \ActiveCollab\DatabaseConnection\ConnectionInterface $connection
 */
trait Implementation
{
    /**
     * Say hello to the parent class
     */
    public function ActiveCollabDatabaseStructureBehaviourPositionInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function() {
            if (!$this->getPosition()) {
                $this->setPosition($this->connection->executeFirstCell('SELECT MAX(`position`) FROM ' . $this->connection->escapeTableName($this->getTableName())) + 1);
            }
        });
    }

    /**
     * @return integer
     */
    abstract public function getPosition();

    /**
     * @param  integer $value
     * @return $this
     */
    abstract public function &setPosition($value);

    /**
     * Return value of table name
     *
     * @return string
     */
    abstract public function getTableName();

    /**
     * Register an internal event handler
     *
     * @param string   $event
     * @param callable $handler
     */
    abstract protected function registerEventHandler($event, callable $handler);
}
