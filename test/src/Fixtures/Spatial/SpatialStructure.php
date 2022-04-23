<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Spatial;

use ActiveCollab\DatabaseStructure\Field\Spatial\PolygonField;
use ActiveCollab\DatabaseStructure\Structure;

class SpatialStructure extends Structure
{
    protected function configure()
    {
        $this
            ->addType('spatial_entities')
                ->addFields(
                    new PolygonField('polygon')
                );
    }
}
