<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface DefaultValueInterface
{
    /**
     * Return default field value.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * @param  mixed $value
     * @return $this
     */
    public function &defaultValue($value);
}
