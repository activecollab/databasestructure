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
    public function getDeserializingCode($variable_name)
    {
        return 'json_decode($' . $variable_name . ', true)';
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name)
    {
        return '$this->isLoading() ? $' . $variable_name . ' : json_encode($' . $variable_name . ')';
    }
}
