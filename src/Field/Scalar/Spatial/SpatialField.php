<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Spatial;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;

abstract class SpatialField extends ScalarField
{
    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_SPATIAL;
    }

    public function getCastingCode($variable_name): string
    {
        return sprintf('$%s', $variable_name);
    }
}
