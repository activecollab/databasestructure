<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

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
     * @var
     */
    private $add_index_context;

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
     * @param  boolean    $add_index
     * @param  array|null $context
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = [])
    {
        $this->add_index = (boolean) $add_index;
        $this->add_index_context = $context;

        return $this;
    }
}