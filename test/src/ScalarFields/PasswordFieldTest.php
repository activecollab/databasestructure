<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\PasswordField;
use ActiveCollab\DatabaseStructure\Test\TestCase;

class PasswordFieldTest extends TestCase
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
