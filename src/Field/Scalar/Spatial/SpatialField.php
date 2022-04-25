<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Spatial;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseConnection\Spatial\WktParser\WktParser;
use ActiveCollab\DatabaseStructure\Field\Scalar\ScalarField;

abstract class SpatialField extends ScalarField
{
    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_SPATIAL;
    }

    public function getCastingCode(string $variable_name): string
    {
        return sprintf(
            '$this->isLoading() && $%s !== null ? (new \\%s)->geomFromText($%s) : $%s',
            $variable_name,
            WktParser::class,
            $variable_name,
            $variable_name,
        );
    }

    public function getSqlReadStatement(string $table_name): string
    {
        return sprintf(
            "ST_ASTEXT(`%s`.`%s`) AS '%s'",
            $table_name,
            $this->getName(),
            $this->getName()
        );
    }
}
