<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\JsonField
 */
class BoolValueExtractor extends ValueExtractor
{
    /**
     * {@inheritdoc}
     */
    public function getValueCaster()
    {
        return ValueCasterInterface::CAST_BOOL;
    }
}
