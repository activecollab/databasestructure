<?php

namespace ActiveCollab\DatabaseStructure\Field;

use ActiveCollab\DatabaseStructure\Index;

/**
 * @package ActiveCollab\DatabaseStructure\Field
 */
interface AddIndexInterface
{
    /**
     * Return true if field that implements this interface should add an index or not
     *
     * @return boolean
     */
    public function getAddIndex();

    /**
     * Return additional index fields
     *
     * @return array|null
     */
    public function getAddIndexContext();

    /**
     * Return add index type
     *
     * @return string
     */
    public function getAddIndexType();

    /**
     * @param  boolean $add_index
     * @param  array   $context
     * @param  string  $type
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = [], $type = Index::INDEX);
}