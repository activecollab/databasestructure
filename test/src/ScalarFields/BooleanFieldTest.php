<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;
use ActiveCollab\DatabaseStructure\Test\TestCase;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class BooleanFieldTest extends TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testExceptionWhenBooleanFieldIsUnique()
    {
        (new BooleanField('should_not_be_required'))->unique();
    }

    /**
     * Test if default value is false.
     */
    public function testDefaultValueIsFalse()
    {
        $this->assertFalse((new BooleanField('is_he_a_pirate'))->getDefaultValue());
    }

    /**
     * Test if default value can be changed to NULL.
     */
    public function testDefaultValueCanBeChagnedToNull()
    {
        $this->assertNull((new BooleanField('should_be_null_by_default'))->defaultValue(null)->getDefaultValue());
    }
}
