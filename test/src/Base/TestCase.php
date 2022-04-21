<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Base;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\DateValue\DateTimeValueInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?DateTimeValueInterface $now = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->setNow(new DateTimeValue());
    }

    public function tearDown(): void
    {
        $this->setNow(null);

        parent::tearDown();
    }

    protected function setNow(DateTimeValueInterface $now = null)
    {
        $this->now = $now;
        DateTimeValue::setTestNow($this->now);
    }
}
