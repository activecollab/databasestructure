<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\FloatField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;

class FloatFieldDbTest extends DbTestCase
{
    public function testNativeType()
    {
        $this->assertSame('float', (new FloatField('float_field'))->getNativeType());
    }
}
