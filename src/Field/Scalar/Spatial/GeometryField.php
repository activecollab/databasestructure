<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Spatial;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseConnection\Spatial\GeometryCollection\GeometryCollectionInterface;

class GeometryField extends SpatialField
{
    public function getNativeType(): string
    {
        return '\\' . GeometryCollectionInterface::class;
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return 'GEOMETRY';
    }
}
