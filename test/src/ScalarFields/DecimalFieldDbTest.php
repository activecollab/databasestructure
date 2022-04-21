<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\DecimalField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;

class DecimalFieldDbTest extends DbTestCase
{
    public function testNativeType()
    {
        $this->assertSame('float', (new DecimalField('float_field'))->getNativeType());
    }
}
