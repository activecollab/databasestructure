<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\Index;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait AddIndex
{
    /**
     * @var boolean
     */
    private $add_index = false;

    /**
     * @var array
     */
    private $add_index_context;

    /**
     * @var string
     */
    private $add_index_type;

    /**
     * Return whether we should add an index for this field or not, defualt is FALSE
     *
     * @return string
     */
    public function getAddIndex()
    {
        return $this->add_index;
    }

    /**
     * @return array|null
     */
    public function getAddIndexContext()
    {
        return $this->add_index_context;
    }

    /**
     * Return add index type
     *
     * @return string
     */
    public function getAddIndexType()
    {
        return $this->add_index_type;
    }

    /**
     * @param  boolean    $add_index
     * @param  array|null $context
     * @param  string     $type
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = [], $type = Index::INDEX)
    {
        $this->add_index = (boolean) $add_index;
        $this->add_index_context = $context;
        $this->add_index_type = $type;

        return $this;
    }
}