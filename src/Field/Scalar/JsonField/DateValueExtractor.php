<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;

class DateValueExtractor extends ValueExtractor
{
    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_DATE;
    }
}
