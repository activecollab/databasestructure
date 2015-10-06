<?php

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseConnection\Result\Result;
use ActiveCollab\DatabaseStructure\TriggerInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
class TriggersBuilder extends DatabaseBuilder implements FileSystemBuilderInterface
{
    use StructureSql;

    /**
     * Build path. If empty, class will be built to memory
     *
     * @var string
     */
    private $build_path;

    /**
     * Return build path
     *
     * @return string
     */
    public function getBuildPath()
    {
        return $this->build_path;
    }

    /**
     * Set build path. If empty, class will be built in memory
     *
     * @param  string $value
     * @return $this
     */
    public function &setBuildPath($value)
    {
        $this->build_path = $value;

        return $this;
    }

    /**
     * @param TypeInterface $type
     */
    public function buildType(TypeInterface $type)
    {
        foreach ($type->getTriggers() as $trigger) {
            $create_trigger_statement = $this->prepareCreateTriggerStatement($type, $trigger);

            $this->appendToStructureSql('DROP TRIGGER IF EXISTS ' . $this->getConnection()->escapeFieldName($trigger->getName()), 'Drop trigger if it already exists');
            $this->appendToStructureSql($create_trigger_statement, 'Create ' . $this->getConnection()->escapeTableName($trigger->getName()) . ' trigger');

            if ($this->triggerExists($trigger->getName())) {
                $this->triggerEvent('on_trigger_exists', [$trigger->getName()]);
            } else {
                $this->getConnection()->execute($create_trigger_statement);
                $this->triggerEvent('on_trigger_created', [$trigger->getName()]);
            }
        }
    }

    /**
     * Prepare belongs to constraint statement
     *
     * @param  TypeInterface    $type
     * @param  TriggerInterface $trigger
     * @return string
     */
    public function prepareCreateTriggerStatement(TypeInterface $type, TriggerInterface $trigger)
    {
        if (strpos($trigger->getBody(), "\n") === false) {
            return 'CREATE TRIGGER ' . $this->getConnection()->escapeFieldName($trigger->getName()) . ' ' . strtoupper($trigger->getTime()) . ' ' . strtoupper($trigger->getEvent()) . ' ON ' . $this->getConnection()->escapeTableName($type->getName()) . ' FOR EACH ROW ' . $trigger->getBody();
        } else {
            $result = [];

            $result[] = 'CREATE TRIGGER ' . $this->getConnection()->escapeFieldName($trigger->getName()) . ' ' . strtoupper($trigger->getTime()) . ' ' . strtoupper($trigger->getEvent()) . ' ON ' . $this->getConnection()->escapeTableName($type->getName());
            $result[] = 'FOR EACH ROW BEGIN';
            $result[] = $trigger->getBody();
            $result[] = 'END;';

            return implode("\n", $result);
        }
    }

    /**
     * Check if trigger exists
     *
     * @param  string $trigger_name
     * @return bool
     */
    private function triggerExists($trigger_name)
    {
        if ($triggers = $this->getConnection()->execute('SHOW TRIGGERS LIKE ?', $trigger_name)) {
            return $triggers instanceof Result && $triggers->count();
        }

        return false;
    }
}