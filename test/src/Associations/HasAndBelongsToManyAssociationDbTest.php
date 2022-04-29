<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations;

use ActiveCollab\DatabaseStructure\Association\HasAndBelongsToManyAssociation;
use ActiveCollab\DatabaseStructure\Test\Base\DbTestCase;
use ActiveCollab\DatabaseStructure\Type;

class HasAndBelongsToManyAssociationDbTest extends DbTestCase
{
    /**
     * Return field names.
     */
    public function testFieldNames()
    {
        $writers = new Type('writers');
        $books = new Type('books');

        $book_writers = new HasAndBelongsToManyAssociation($writers->getName());
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

        $book_writers = new HasAndBelongsToManyAssociation($writers->getName());
        $books->addAssociation($book_writers);

        $this->assertEquals('book_id_for_books_writers_constraint', $book_writers->getVerboseLeftConstraintName());
        $this->assertEquals(
            'has_and_belongs_to_many_' . md5('book_id_for_books_writers_constraint'),
            $book_writers->getLeftConstraintName()
        );
        $this->assertEquals('writer_id_for_books_writers_constraint', $book_writers->getVerboseRightConstraintName());
        $this->assertEquals(
            'has_and_belongs_to_many_' . md5('writer_id_for_books_writers_constraint'),
            $book_writers->getRightConstraintName()
        );
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
