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
     * Return whether we should add an index for this field or not, defualt is FALSE
     *
     * @return string
     */
    public function getAddIndex()
    {
        return $this->add_index;
    }

    /**
     * @param  boolean $add_index
     * @return $this
     */
    public function &addIndex($add_index)
    {
        $this->add_index = (boolean) $add_index;

        return $this;
    }
}