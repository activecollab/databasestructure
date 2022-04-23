<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;

class DateTimeValueExtractor extends ValueExtractor
{
    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_DATETIME;
    }
}
