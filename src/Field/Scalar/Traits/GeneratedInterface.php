<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface GeneratedInterface
{
    /**
     * Return true if value of this field is generated in the background.
     *
     * @return bool
     */
    public function isGenerated();

    /**
     * Value of this column is generated in the background.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &generated($value = true);
}
