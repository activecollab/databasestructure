<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Spatial;

use ActiveCollab\DatabaseStructure\Spatial\Coordinates\CoordinateInterface;

interface PolygonInterface
{
    /**
     * @return CoordinateInterface[]
     */
    public function getCoordinates(): array;
}
