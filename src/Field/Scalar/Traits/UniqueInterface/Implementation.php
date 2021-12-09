<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\AddIndexInterface;
use ActiveCollab\DatabaseStructure\IndexInterface;

trait Implementation
{
    /**
     * @var bool
     */
    private $is_unique = false;

    /**
     * @var array
     */
    private $uniquness_context = [];

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return $this->is_unique;
    }

    /**
     * {@inheritdoc}
     */
    public function getUniquenessContext()
    {
        return $this->uniquness_context;
    }

    /**
     * {@inheritdoc}
     */
    public function &unique(...$context)
    {
        $this->is_unique = true;
        $this->uniquness_context = $context;

        if ($this instanceof AddIndexInterface) {
            $this->addIndex(true, $context, IndexInterface::UNIQUE);
        }

        return $this;
    }
}
