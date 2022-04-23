<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Spatial;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseConnection\Spatial\Polygon\PolygonInterface;

class PolygonField extends SpatialField
{
    public function getNativeType(): string
    {
        return PolygonInterface::class;
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return 'POLYGON';
    }

    public function getSqlReadStatement(): string
    {
        return sprintf(
            "ST_GEOMFROMTEXT(`%s`) AS '%s'",
            $this->getName(),
            $this->getName()
        );
    }
}
