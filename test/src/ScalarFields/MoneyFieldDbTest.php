<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\ScalarFields;

use ActiveCollab\DatabaseStructure\Field\Scalar\DecimalField;
use ActiveCollab\DatabaseStructure\Field\Scalar\MoneyField;
use ActiveCollab\DatabaseStructure\Field\Scalar\NumberField;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ReflectionClass;

class MoneyFieldDbTest extends DbTestCase
{
    /**
     * Test if money field extends decimal field.
     */
    public function testIntegerExtendsNumber()
    {
        $this->assertTrue((new ReflectionClass(MoneyField::class))->isSubclassOf(NumberField::class));
        $this->assertTrue((new ReflectionClass(MoneyField::class))->isSubclassOf(DecimalField::class));
    }

    /**
     * Test if moeny fields are not unsigned by default.
     */
    public function testNotUnsignedByDefault()
    {
        $this->assertFalse((new MoneyField('amount'))->isUnsigned());
    }

    /**
     * Test if money fields can be set to be unsigned.
     */
    public function testFieldCanBeSetToBeUnsigned()
    {
        $this->assertTrue((new MoneyField('amount'))->unsigned(true)->isUnsigned());
    }

    /**
     * Check if size is 12:6 by default.
     */
    public function testDefaultSize()
    {
        $amount = new MoneyField('amount');

        $this->assertSame(12, $amount->getLength());
        $this->assertSame(6, $amount->getScale());
    }
}
