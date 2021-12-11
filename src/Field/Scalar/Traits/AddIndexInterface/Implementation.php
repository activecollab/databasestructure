<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;

use ActiveCollab\DatabaseStructure\IndexInterface;

trait Implementation
{
    /**
     * @var bool
     */
    private bool $add_index = false;

    /**
     * @var array
     */
    private $add_index_context = [];

    /**
     * @var string
     */
    private $add_index_type = IndexInterface::INDEX;

    /**
     * Return whether we should add an index for this field or not, default is FALSE.
     */
    public function getAddIndex(): bool
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
     */
    public function getAddIndexType(): string
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
