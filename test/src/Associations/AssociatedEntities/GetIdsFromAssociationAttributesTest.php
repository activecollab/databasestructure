<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations\AssociatedEntities;

use ActiveCollab\DatabaseStructure\Entity\EntityInterface;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\WriterHasManyBookIdsStructure;
use ActiveCollab\DatabaseStructure\Test\StructuredTestCase;

final class GetIdsFromAssociationAttributesTest extends StructuredTestCase
{
    protected function getStructureClassName(): string
    {
        return WriterHasManyBookIdsStructure::class;
    }

    protected function getBuildPath(): string
    {
        return dirname(dirname(__DIR__)) . '/Fixtures/Association/WriterHasManyBookIds';
    }

    public function testIdsFromObjects()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);
        $this->assertTrue($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);
        $this->assertTrue($book2->isLoaded());

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);
        $this->assertTrue($book3->isLoaded());

        $writer->setAttribute('books', [$book1, $book2, $book3]);

        $this->assertSame([1, 2, 3], $writer->getIdsFromAssociationAttributes('books'));
    }

    public function testIdsFromObjectsAreSorted()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);
        $this->assertTrue($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);
        $this->assertTrue($book2->isLoaded());

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);
        $this->assertTrue($book3->isLoaded());

        $writer->setAttribute('books', [$book2, $book3, $book1]);

        $this->assertSame([1, 2, 3], $writer->getIdsFromAssociationAttributes('books'));
    }

    public function testFromObjectsAreNotDuplicated()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);
        $this->assertTrue($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);
        $this->assertTrue($book2->isLoaded());

        $writer->setAttribute('books', [$book1, $book1, $book1, $book2]);

        $this->assertSame([1, 2], $writer->getIdsFromAssociationAttributes('books'));
    }

    public function testObjectsAreSavedOnGetIds()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ], false);
        $this->assertFalse($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ], false);
        $this->assertFalse($book2->isLoaded());

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ], false);
        $this->assertFalse($book3->isLoaded());

        $writer->setAttribute('books', [$book1, $book2, $book3]);

        $this->assertSame([1, 2, 3], $writer->getIdsFromAssociationAttributes('books'));

        $this->assertTrue($book1->isLoaded());
        $this->assertTrue($book2->isLoaded());
        $this->assertTrue($book3->isLoaded());
    }

    public function testIdsFromIds()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);
        $this->assertTrue($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);
        $this->assertTrue($book2->isLoaded());

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);
        $this->assertTrue($book3->isLoaded());

        $writer->setAttribute('book_ids', [$book1->getId(), $book2->getId(), $book3->getId()]);

        $this->assertSame([1, 2, 3], $writer->getIdsFromAssociationAttributes('books'));
    }

    public function testIdsFromIdsAreSorted()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);
        $this->assertTrue($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);
        $this->assertTrue($book2->isLoaded());

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);
        $this->assertTrue($book3->isLoaded());

        $writer->setAttribute('book_ids', [$book2->getId(), $book3->getId(), $book1->getId()]);

        $this->assertSame([1, 2, 3], $writer->getIdsFromAssociationAttributes('books'));
    }

    public function testIdsFromIdsAreNotDuplicated()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        /** @var EntityInterface $writer */
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);
        $this->assertTrue($book1->isLoaded());

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);
        $this->assertTrue($book2->isLoaded());

        $writer->setAttribute('book_ids', [$book1->getId(), $book1->getId(), $book1->getId(), $book2->getId()]);

        $this->assertSame([1, 2], $writer->getIdsFromAssociationAttributes('books'));
    }
}
