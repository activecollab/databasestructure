<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;

use ActiveCollab\DatabaseStructure\Behaviour\PositionInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\PositionInterface
 * @property \ActiveCollab\DatabaseConnection\ConnectionInterface $connection
 * @property \ActiveCollab\DatabaseObject\PoolInterface $pool
 */
trait Implementation
{
    /**
     * Say hello to the parent class.
     */
    public function ActiveCollabDatabaseStructureBehaviourPositionInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function () {
            if (!$this->getPosition()) {
                $table_name = $this->connection->escapeTableName($this->getTableName());
                $conditions = $this->getPositionContextConditions();

                if ($this->getPositionMode() == PositionInterface::POSITION_MODE_HEAD) {
                    $this->setPosition(1);

                    if ($ids = $this->connection->executeFirstColumn("SELECT `id` FROM $table_name $conditions")) {
                        $this->connection->execute("UPDATE $table_name SET `position` = `position` + 1 $conditions");
                    }
                } else {
                    $this->setPosition($this->connection->executeFirstCell("SELECT MAX(`position`) FROM $table_name $conditions") + 1);
                }
            }
        });
    }

    /**
     * Return position context conditions.
     *
     * @return string
     */
    private function getPositionContextConditions()
    {
        $pattern = $this->pool->getTypeProperty(get_class($this), 'position_context_conditions_pattern', function () {
            $conditions = [];

            foreach ($this->getPositionContext() as $field_name) {
                $conditions[] = $this->connection->escapeFieldName($field_name) . ' = ?';
            }

            return count($conditions) ? implode(' AND ', $conditions) : '';
        });

        if ($pattern) {
            $to_prepare = [$pattern];

            foreach ($this->getPositionContext() as $field_name) {
                $to_prepare[] = $this->getFieldValue($field_name);
            }

            return 'WHERE ' . call_user_func_array([&$this->connection, 'prepare'], $to_prepare);
        }

        return '';
    }

    /**
     * @return int
     */
    abstract public function getPosition();

    /**
     * @param  int   $value
     * @return $this
     */
    abstract public function &setPosition(int $value);

    /**
     * Return position mode.
     *
     * There are two modes:
     *
     * * head for new records to be added in front of the other records, or
     * * tail when new records are added after existing records.
     *
     * @return string
     */
    abstract public function getPositionMode();

    /**
     * Return context in which position should be set.
     *
     * @return array
     */
    abstract public function getPositionContext();

    /**
     * Return value of table name.
     *
     * @return string
     */
    abstract public function getTableName();

    /**
     * Return value of specific field and typecast it...
     *
     * @param  string $field   Field value
     * @param  mixed  $default Default value that is returned in case of any error
     * @return mixed
     */
    abstract public function getFieldValue($field, $default = null);

    /**
     * Register an internal event handler.
     *
     * @param string   $event
     * @param callable $handler
     */
    abstract protected function registerEventHandler($event, callable $handler);
}
