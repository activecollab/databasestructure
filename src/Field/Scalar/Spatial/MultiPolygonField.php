<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Spatial;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseConnection\Spatial\MultiPolygon\MultiPolygonInterface;

class MultiPolygonField extends SpatialField
{
    public function getNativeType(): string
    {
        return '\\' . MultiPolygonInterface::class;
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return 'MULTIPOLYGON';
    }
}