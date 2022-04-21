<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Spatial;

use ActiveCollab\DatabaseStructure\Spatial\Coordinates\CoordinateInterface;

class Polygon implements PolygonInterface
{
    private CoordinateInterface $coordinates;

    public function __construct(
        CoordinateInterface ...$coordinates
    )
    {
        $this->coordinates = $coordinates;
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }
}
