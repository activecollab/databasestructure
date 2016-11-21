<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;

use ActiveCollab\DatabaseStructure\IndexInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Implementation
{
    /**
     * @var bool
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
     * Return whether we should add an index for this field or not, defualt is FALSE.
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
     * Return add index type.
     *
     * @return string
     */
    public function getAddIndexType()
    {
        return $this->add_index_type;
    }

    /**
     * @param  bool       $add_index
     * @param  array|null $context
     * @param  string     $type
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = [], $type = IndexInterface::INDEX)
    {
        $this->add_index = (bool) $add_index;
        $this->add_index_context = $context;
        $this->add_index_type = $type;

        return $this;
    }
}
