<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;
use ActiveCollab\DatabaseStructure\Test\TestCase;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class JsonFieldTest extends TestCase
{
    /**
     * Test if native type is mixed.
     */
    public function testMixedNativeType()
    {
        $this->assertEquals('mixed', (new JsonField('test_feild'))->getNativeType());
    }

    /**
     * Test if json_encode() is used for data serialization.
     */
    public function testCastingUsesJsonDecode()
    {
        $this->assertContains('json_encode', (new JsonField('test_feild'))->getCastingCode('value'));
    }
}
