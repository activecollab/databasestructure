<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use ActiveCollab\DatabaseStructure\MultiRecordInterface;
use ActiveCollab\DatabaseStructure\RecordInterface;

class RecordsBuilder extends DatabaseBuilder implements FileSystemBuilderInterface
{
    use StructureSql;

    /**
     * Build path. If empty, class will be built to memory.
     *
     * @var string
     */
    private $build_path;

    /**
     * Return build path.
     *
     * @return string
     */
    public function getBuildPath()
    {
        return $this->build_path;
    }

    /**
     * Set build path. If empty, class will be built in memory.
     *
     * @param  string $value
     * @return $this
     */
    public function &setBuildPath($value)
    {
        $this->build_path = $value;

        return $this;
    }

    public function postBuild(): void
    {
        /** @var RecordInterface $record */
        foreach ($this->getStructure()->getRecords() as $record) {
            $insert_statement = $this->prepareInsertStatement($record);

            $this->appendToInitialDataSql($insert_statement, $record->getComment());

            $this->getConnection()->execute($insert_statement);
            $this->triggerEvent('on_record_inserted', [$this->getInsertMessage($record)]);
        }
    }

    private function prepareInsertStatement(RecordInterface $record)
    {
        $prepared_fields = $this->prepareFieldNames($record->getFields());

        if ($record->getAutoSetCreatedAt()) {
            $prepared_fields .= ',' . $this->getConnection()->escapeFieldName('created_at');
        }

        if ($record->getAutoSetUpdatedAt()) {
            $prepared_fields .= ',' . $this->getConnection()->escapeFieldName('updated_at');
        }

        $statement = "INSERT INTO {$this->getConnection()->escapeTableName($record->getTableName())} ({$prepared_fields}) VALUES\n";

        if ($record instanceof MultiRecordInterface) {
            foreach ($record->getValues() as $v) {
                $prepared_values = $this->prepareValues($v);

                if ($record->getAutoSetCreatedAt()) {
                    $prepared_values .= ',UTC_TIMESTAMP()';
                }

                if ($record->getAutoSetUpdatedAt()) {
                    $prepared_values .= ',UTC_TIMESTAMP()';
                }

                $statement .= "    ({$prepared_values}),\n";
            }
        } else {
            $prepared_values = $this->prepareValues($record->getValues());

            if ($record->getAutoSetCreatedAt()) {
                $prepared_values .= ',UTC_TIMESTAMP()';
            }

            if ($record->getAutoSetUpdatedAt()) {
                $prepared_values .= ',UTC_TIMESTAMP()';
            }

            $statement .= "    ({$prepared_values}),\n";
        }

        return rtrim(rtrim($statement, "\n"), ',') . ";\n";
    }

    /**
     * @param  array  $field_names
     * @return string
     */
    private function prepareFieldNames(array $field_names)
    {
        return implode(',', array_map(function($field_name) {
            return $this->getConnection()->escapeFieldName($field_name);
        }, $field_names));
    }

    /**
     * @param  array  $values
     * @return string
     */
    private function prepareValues(array $values)
    {
        return implode(',', array_map(function($value) {
            return $this->getConnection()->escapeValue($value);
        }, $values));
    }

    private function getInsertMessage(RecordInterface $record)
    {
        $records_to_insert = 1;

        if ($record instanceof MultiRecordInterface) {
            $records_to_insert = count($record->getValues());
        }

        if ($records_to_insert > 1) {
            $message = "Inserting {$records_to_insert} records into {$record->getTableName()} table.";
        } else {
            $message = "Inserting a record into {$record->getTableName()} table.";
        }

        return $message;
    }
}
