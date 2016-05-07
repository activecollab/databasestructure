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
interface LengthInterface extends FieldTraitInterface
{
    /**
     * Return length of the field, if set.
     *
     * @return int
     */
    public function getLength();

    /**
     * @param  int   $length
     * @return $this
     */
    public function &length($length);
}
