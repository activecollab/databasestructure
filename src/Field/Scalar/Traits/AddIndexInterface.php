<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\IndexInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
interface AddIndexInterface extends FieldTraitInterface
{
    /**
     * Return whether we should add an index for this field or not, defualt is FALSE.
     *
     * @return string
     */
    public function getAddIndex();

    /**
     * @return array|null
     */
    public function getAddIndexContext();

    /**
     * Return add index type.
     */
    public function getAddIndexType(): string;

    /**
     * @param  bool       $add_index
     * @param  array|null $context
     * @param  string     $type
     * @return $this
     */
    public function &addIndex($add_index = true, array $context = [], $type = IndexInterface::INDEX);
}
