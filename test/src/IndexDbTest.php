<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\IndexInterface;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use InvalidArgumentException;

class IndexDbTest extends DbTestCase
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

    /**
     * Test valid field arguments.
     */
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
