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
use InvalidArgumentException;

final class HasManyAssociationAssociatedEntitiesTest extends StructuredTestCase
{
    protected function getStructureClassName(): string
    {
        return WriterHasManyBooksStructure::class;
    }

    protected function getBuildPath(): string
    {
        return dirname(dirname(__DIR__)) . '/Fixtures/Association/WriterHasManyBooks';
    }

    public function testNonIterableAttribute()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A list of entities expected.");

        $this->pool->produce($this->type_entity_class_names['writers'], [
            'name' => 'Leo Tolstoy',
            'books' => 123,
        ]);
    }

    public function testInvalidAttributeInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A list of entities expected.");

        $this->pool->produce($this->type_entity_class_names['writers'], [
            'name' => 'Leo Tolstoy',
            'books' => [new \stdClass],
        ]);
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

    public function testNonIterableIdsAttribute()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A list of ID-s expected.");

        $this->pool->produce($this->type_entity_class_names['writers'], [
            'name' => 'Leo Tolstoy',
            'book_ids' => 123,
        ]);
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

    public function testAssociatedEntityIdsAttributeUpdatesOnInsert()
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

        // Set three books.
        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'book_ids' => [$book1->getId(), $book2->getId(), $book3->getId()],
        ], false);
        $this->assertFalse($writer->isLoaded());

        // Reset to one book
        $writer->setAttribute('book_ids', [$book2->getId()]);
        $writer->save();

        $this->assertTrue($writer->isLoaded());
        $this->assertSame(1, $writer->countBooks());
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

    public function testAssociatedEntityAttributeResetOnUpdate()
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

        $book_ids = $writer->getBookIds();
        $this->assertCount(2, $book_ids);

        $this->assertContains($book1->getId(), $book_ids);
        $this->assertContains($book2->getId(), $book_ids);
        $this->assertNotContains($book3->getId(), $book_ids);

        $writer = $this->pool->modify($writer, [
            'books' => [],
        ]);
        $this->assertTrue($writer->isLoaded());

        $this->assertSame(0, $writer->countBooks());
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
