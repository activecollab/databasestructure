<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Associations\AssociatedEntities;

use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\WriterHasAndBelongsToManyBooksStructure;
use ActiveCollab\DatabaseStructure\Test\StructuredTestCase;

class HasAndBelongsToManyAssociationAssociatedEntitiesTest extends StructuredTestCase
{
    protected function getStructureClassName(): string
    {
        return WriterHasAndBelongsToManyBooksStructure::class;
    }

    protected function getBuildPath(): string
    {
        return dirname(dirname(__DIR__)) . '/Fixtures/Association/WriterHasAndBelongsToManyBooks';
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
        $this->assertFalse($book2->isLoaded());
        $this->assertFalse($book3->isLoaded());

        $writer = $this->pool->produce($writer_entity_class_name, [
            'name' => 'Leo Tolstoy',
            'books' => [$book1, $book2],
        ]);
        $this->assertTrue($writer->isLoaded());

        $this->assertTrue($book1->isLoaded());
        $this->assertTrue($book2->isLoaded());
        $this->assertFalse($book3->isLoaded());

        $this->assertSame(2, $writer->countBooks());
    }
}
