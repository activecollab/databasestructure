<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Spatial\Coordinates;

class Longitude implements LongitudeInterface
{
    private float $longitude;

    public function __construct(float $longitude)
    {
        $this->longitude = $longitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
