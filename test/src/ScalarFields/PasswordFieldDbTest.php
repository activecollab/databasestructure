<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\PasswordField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;

class PasswordFieldDbTest extends DbTestCase
{
    public function testLDefaultName()
    {
        $this->assertSame('password', (new PasswordField())->getName());
    }

    public function testLRequiredByDefault()
    {
        $this->assertTrue((new PasswordField())->isRequired());
    }
}
