<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;
use InvalidArgumentException;

class IndexTest extends TestCase
{
    public function testIndexNameIsRequired()
    {
        $this->expectException(InvalidArgumentException::class);

        new Index('');
    }

    /**
     * Test if index name is used as a field by default.
     */
    public function testDefaultFieldIsIndexName()
    {
        $index = new Index('is_awesome');

        $this->assertCount(1, $index->getFields());
        $this->assertContains('is_awesome', $index->getFields());
    }

    public function testNullOrArrayAllowedForFields()
    {
        $this->expectNotToPerformAssertions();

        new Index('is_awesome', null);
        new Index('is_awesome', []);
        new Index('is_awesome', ['name']);
    }

    /**
     * Test default index type.
     */
    public function testDefaultIndexType()
    {
        $index = new Index('is_awesome');
        $this->assertEquals(IndexInterface::INDEX, $index->getIndexType());
    }

    /**
     * Test index type is applied when set.
     */
    public function testIndexTypeCanBeSet()
    {
        $index = new Index('id', null, IndexInterface::PRIMARY);
        $this->assertEquals(IndexInterface::PRIMARY, $index->getIndexType());
    }

    public function testInvalidIndexTypeThrowsAnException()
    {
        $this->expectException(InvalidArgumentException::class);

        new Index('is_awesome', null, 'invalid index type');
    }
}
