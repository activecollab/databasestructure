<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\IndexInterface;

interface AddIndexInterface extends FieldTraitInterface
{
    /**
     * Return whether we should add an index for this field or not, default is FALSE.
     */
    public function getAddIndex(): bool;

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
