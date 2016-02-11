<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
interface UniqueInterface extends FieldTraitInterface
{
    /**
     * Return true if this field should be unique.
     *
     * @return bool
     */
    public function isUnique();

    /**
     * Return uniqueness context.
     *
     * @return array
     */
    public function getUniquenessContext();

    /**
     * Value of this column needs to be unique (in the given context).
     *
     * @param  string $context
     * @return $this
     */
    public function &unique(...$context);
}
