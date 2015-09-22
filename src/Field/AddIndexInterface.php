<?php

namespace ActiveCollab\DatabaseStructure\Field;

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
     * @param  boolean $add_index
     * @param  array   $context
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = []);
}