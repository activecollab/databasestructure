<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Spatial\Coordinates;

class Coordinate implements CoordinateInterface
{
    public function __construct(
        private LatitudeInterface $latitude,
        private LongitudeInterface $longitude,
    )
    {
    }

    public function getLatitude(): LatitudeInterface
    {
        return $this->latitude;
    }

    public function getLongitude(): LongitudeInterface
    {
        return $this->longitude;
    }
}
