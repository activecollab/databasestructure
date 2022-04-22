<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Spatial;

use ActiveCollab\DatabaseStructure\Spatial\Coordinates\CoordinateInterface;
use LogicException;

class Polygon implements PolygonInterface
{
    /**
     * @var CoordinateInterface[]
     */
    private array $coordinates;

    public function __construct(
        CoordinateInterface ...$coordinates
    )
    {
        if (count($coordinates) < 4) {
            throw new LogicException('At least four coordinates are required.');
        }

        if (!$coordinates[0]->isSame($coordinates[count($coordinates) - 1])) {
            throw new LogicException('Polygon is not closed.');
        }

        $this->coordinates = $coordinates;
    }

    public function getCoordinates(): array
    {
        return $this->coordinates;
    }
}
