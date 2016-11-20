<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Test\TestCase;
use ActiveCollab\DatabaseStructure\Type;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class HasAndBelongsToManyAssociationTest extends TestCase
{
    /**
     * Return field names.
     */
    public function testFieldNames()
    {
        $writers = new Type('writers');
        $books = new Type('books');
        $book_writers = new HasAndBelongsToManyAssociation('writers');
        $books->addAssociation($book_writers);

        $this->assertEquals('book_id', $book_writers->getLeftFieldName());
        $this->assertEquals('writer_id', $book_writers->getRightFieldName());
    }

    /**
     * Test constraint name for belongs to association.
     */
    public function testConstraintNames()
    {
        $writers = new Type('writers');
        $books = new Type('books');
        $book_writers = new HasAndBelongsToManyAssociation('writers');
        $books->addAssociation($book_writers);

        $this->assertEquals('book_id_constraint', $book_writers->getLeftConstraintName());
        $this->assertEquals('writer_id_constraint', $book_writers->getRightConstraintName());
    }

    /**
     * Test how connection table name is prepared.
     */
    public function testConnectionTableName()
    {
        $writers = new Type('writers');
        $books = new Type('books');
        $book_can_have_many_writers = new HasAndBelongsToManyAssociation('writers');
        $books->addAssociation($book_can_have_many_writers);

        $writer_can_have_many_books = new HasAndBelongsToManyAssociation('books');
        $writers->addAssociation($writer_can_have_many_books);

        $this->assertEquals('books_writers', $book_can_have_many_writers->getConnectionTableName());
        $this->assertEquals('books_writers', $writer_can_have_many_books->getConnectionTableName());
    }
}
