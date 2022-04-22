<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\SpatialFields;

use ActiveCollab\DatabaseStructure\Field\Spatial\PolygonField;
use ActiveCollab\DatabaseStructure\Spatial\PolygonInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;

class PolygonFieldDbTest extends DbTestCase
{
    public function testWillUsePolygonsAsNativeType(): void
    {
        $this->assertSame(
            PolygonInterface::class,
            (new PolygonField('polygon'))->getNativeType()
        );
    }
}
