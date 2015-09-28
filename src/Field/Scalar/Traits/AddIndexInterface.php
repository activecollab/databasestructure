<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\IndexInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
interface AddIndexInterface
{
    /**
     * Return whether we should add an index for this field or not, defualt is FALSE
     *
     * @return string
     */
    public function getAddIndex();

    /**
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
     * @param  boolean    $add_index
     * @param  array|null $context
     * @param  string     $type
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = [], $type = IndexInterface::INDEX);
}