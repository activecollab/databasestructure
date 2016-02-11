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
interface SizeInterface extends FieldTraitInterface
{
    /**
     * Return size of the field, if set.
     *
     * @return string
     */
    public function getSize();

    /**
     * @param  string $size
     * @return $this
     */
    public function &size($size);
}
