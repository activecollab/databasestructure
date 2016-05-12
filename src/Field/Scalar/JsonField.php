<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class JsonField extends Field
{
    /**
     * {@inheritdoc}
     */
    public function getNativeType()
    {
        return 'mixed';
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name)
    {
        return 'json_decode($' . $variable_name . ')';
    }
}
