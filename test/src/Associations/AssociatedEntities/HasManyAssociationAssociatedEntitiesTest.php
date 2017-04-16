<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations\AssociatedEntities;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBooks\WriterHasManyBooksStructure;
use ActiveCollab\DatabaseStructure\Test\StructuredTestCase;

class HasManyAssociationAssociatedEntitiesTest extends StructuredTestCase
{
    protected function getStructureClassName(): string
    {
        return WriterHasManyBooksStructure::class;
    }

    protected function getBuildPath(): string
    {
        return dirname(dirname(__DIR__)) . '/Fixtures/Association/WriterHasManyBooks';
    }

    public function testAssociatedEntitiesAttributeOnInsert()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ], false);

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ], false);

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ], false);

        $this->assertFalse($book1->isLoaded());
        $this->assertNull($book1->getWriterId());
        $this->assertFalse($book2->isLoaded());
        $this->assertNull($book2->getWriterId());
        $this->assertFalse($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'books' => [$book1, $book2],
        ]);
        $this->assertTrue($writer->isLoaded());

        $this->assertTrue($book1->isLoaded());
        $this->assertSame($writer->getId(), $book1->getWriterId());
        $this->assertTrue($book2->isLoaded());
        $this->assertSame($writer->getId(), $book2->getWriterId());
        $this->assertFalse($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $this->assertSame(2, $writer->countBooks());
    }

    public function testExistingAssociatedEntitiesAttributeOnInsert()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);

        $this->assertTrue($book1->isLoaded());
        $this->assertNull($book1->getWriterId());
        $this->assertTrue($book2->isLoaded());
        $this->assertNull($book2->getWriterId());
        $this->assertTrue($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'books' => [$book1, $book2],
        ]);
        $this->assertTrue($writer->isLoaded());

        $this->assertSame(2, $writer->countBooks());
    }

    public function testAssociatedEntityIdsAttributeOnInsert()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);

        $this->assertTrue($book1->isLoaded());
        $this->assertNull($book1->getWriterId());
        $this->assertTrue($book2->isLoaded());
        $this->assertNull($book2->getWriterId());
        $this->assertTrue($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'book_ids' => [$book1->getId(), $book2->getId()],
        ]);
        $this->assertTrue($writer->isLoaded());

        $this->assertSame(2, $writer->countBooks());
    }

    public function testAssociatedEntitiesAttributeOnUpdate()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($writer->isLoaded());
        $this->assertSame(0, $writer->countBooks());

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ], false);

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ], false);

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ], false);

        $this->assertFalse($book1->isLoaded());
        $this->assertNull($book1->getWriterId());
        $this->assertFalse($book2->isLoaded());
        $this->assertNull($book2->getWriterId());
        $this->assertFalse($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $this->pool->modify($writer, [
            'books' => [$book1, $book2],
        ]);

        $this->assertTrue($book1->isLoaded());
        $this->assertSame($writer->getId(), $book1->getWriterId());
        $this->assertTrue($book2->isLoaded());
        $this->assertSame($writer->getId(), $book2->getWriterId());
        $this->assertFalse($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $this->assertSame(2, $writer->countBooks());
    }

    public function testAssociatedEntityIdsAttributeOnUpdate()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);

        $this->assertTrue($book1->isLoaded());
        $this->assertNull($book1->getWriterId());
        $this->assertTrue($book2->isLoaded());
        $this->assertNull($book2->getWriterId());
        $this->assertTrue($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'book_ids' => [$book1->getId(), $book2->getId()],
        ]);
        $this->assertTrue($writer->isLoaded());

        $book_ids = $writer->getBookIds();
        $this->assertCount(2, $book_ids);

        $this->assertContains($book1->getId(), $book_ids);
        $this->assertContains($book2->getId(), $book_ids);
        $this->assertNotContains($book3->getId(), $book_ids);

        $writer = $this->pool->modify($writer, [
            'book_ids' => [$book2->getId(), $book3->getId()],
        ]);
        $this->assertTrue($writer->isLoaded());

        $book_ids = $writer->getBookIds();
        $this->assertCount(2, $book_ids);

        $this->assertNotContains($book1->getId(), $book_ids);
        $this->assertContains($book2->getId(), $book_ids);
        $this->assertContains($book3->getId(), $book_ids);
    }

    public function testAssociatedEntityIdsAttributeResetOnUpdate()
    {
        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
        ]);

        $book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
        ]);

        $book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
        ]);

        $this->assertTrue($book1->isLoaded());
        $this->assertNull($book1->getWriterId());
        $this->assertTrue($book2->isLoaded());
        $this->assertNull($book2->getWriterId());
        $this->assertTrue($book3->isLoaded());
        $this->assertNull($book3->getWriterId());

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'book_ids' => [$book1->getId(), $book2->getId()],
        ]);
        $this->assertTrue($writer->isLoaded());

        $book_ids = $writer->getBookIds();
        $this->assertCount(2, $book_ids);

        $this->assertContains($book1->getId(), $book_ids);
        $this->assertContains($book2->getId(), $book_ids);
        $this->assertNotContains($book3->getId(), $book_ids);

        $writer = $this->pool->modify($writer, [
            'book_ids' => [],
        ]);
        $this->assertTrue($writer->isLoaded());

        $this->assertSame(0, $writer->countBooks());
    }
}
