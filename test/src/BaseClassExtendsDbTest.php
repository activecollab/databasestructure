<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Entity\Entity;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Test\Fixtures\BaseClassExtends\BaseClassExtendsStructure;
use ActiveCollab\DatabaseStructure\Test\Fixtures\ExtendThisObject;

class BaseClassExtendsDbTest extends DbTestCase
{
    /**
     * Test default base class extends settings.
     */
    public function testDefaultBaseClassExtends()
    {
        $structure = new BaseClassExtendsStructure();
        $this->assertEmpty($structure->getConfig('base_class_extends'));

        $this->assertEquals(Entity::class, $structure->getType('writers')->getBaseEntityClassExtends());
    }

    /**
     * Test if we can override base class using a config.
     */
    public function testBaseClassExtendsOverride()
    {
        $structure = new BaseClassExtendsStructure(ExtendThisObject::class);
        $this->assertEquals(ExtendThisObject::class, $structure->getConfig('base_class_extends'));

        $this->assertEquals(ExtendThisObject::class, $structure->getType('writers')->getBaseEntityClassExtends());
    }
}
