<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations\AssociatedEntities;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBooksRequired\WriterHasManyBooksRequiredStructure;
use ActiveCollab\DatabaseStructure\Test\StructuredTestCase;
use RuntimeException;

final class RequiredHasManyAssociationAssociatedEntitiesTest extends StructuredTestCase
{
    private $writer;

    private $book1;

    private $book2;

    private $book3;

    protected function getStructureClassName(): string
    {
        return WriterHasManyBooksRequiredStructure::class;
    }

    protected function getBuildPath(): string
    {
        return dirname(dirname(__DIR__)) . '/Fixtures/Association/WriterHasManyBooksRequired';
    }

    public function setUp(): void
    {
        parent::setUp();

        $writer_entity_class_name = $this->type_entity_class_names['writers'];
        $book_entity_class_name = $this->type_entity_class_names['books'];

        $this->writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
        ]);
        $this->assertTrue($this->writer->isLoaded());

        $this->book1 = $this->pool->produce($book_entity_class_name, [
            'name' => 'War and Peace',
            'writer_id' => $this->writer->getId(),
        ]);

        $this->book2 = $this->pool->produce($book_entity_class_name, [
            'name' => 'Anna Karenina',
            'writer_id' => $this->writer->getId(),
        ]);

        $this->book3 = $this->pool->produce($book_entity_class_name, [
            'name' => 'The Government Inspector',
            'writer_id' => $this->writer->getId(),
        ]);

        $this->assertTrue($this->book1->isLoaded());
        $this->assertSame($this->writer->getId(), $this->book1->getWriterId());
        $this->assertTrue($this->book2->isLoaded());
        $this->assertSame($this->writer->getId(), $this->book2->getWriterId());
        $this->assertTrue($this->book3->isLoaded());
        $this->assertSame($this->writer->getId(), $this->book3->getWriterId());

        $book_ids = $this->writer->getBookIds();
        $this->assertCount(3, $book_ids);
    }

    /**
     * @dataProvider provideAttributesForReleaseTest
     * @param string $attribute_name
     * @param mixed  $attribute_value
     */
    public function testExceptionOnReleaseForRequiredAssociation(string $attribute_name, $attribute_value)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Can't release associated entity #1 because it can only be reassigned, not released.");

        $this->pool->modify($this->writer, [
            $attribute_name => $attribute_value,
        ]);
    }

    public function provideAttributesForReleaseTest(): array
    {
        return [
            ['books', []],
            ['book_ids', []],
        ];
    }
}
