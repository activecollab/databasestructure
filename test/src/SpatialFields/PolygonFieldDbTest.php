<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\SpatialFields;

use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Spatial\SpatialStructure;
use ReflectionMethod;

class PolygonFieldDbTest extends DbTestCase
{
    private string $namespace = '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Spatial\\';

    public function setUp(): void
    {
        parent::setUp();

        $structure = new SpatialStructure();

        if (!class_exists("{$this->namespace}SpatialEntity", false)) {
            $structure->build();
        }
    }

    public function testWillBuildSpatialFields(): void
    {
        $reflection = new \ReflectionClass("{$this->namespace}SpatialEntity");

        $entity_fields = $reflection->getProperty('entity_fields')->getDefaultValue();

        $this->assertContains('id', $entity_fields);
        $this->assertContains('polygon', $entity_fields);

        $this->assertInstanceOf(ReflectionMethod::class, $reflection->getMethod('getPolygon'));
        $this->assertInstanceOf(ReflectionMethod::class, $reflection->getMethod('setPolygon'));
    }
}
