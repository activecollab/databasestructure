<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Base;

use ActiveCollab\DatabaseObject\Collection\Type as TypeCollection;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Book;

abstract class BooksManager extends TypeCollection
{
    /**
     * Return type that this collection works with.
     *
     * @return string
     */
    public function getType()
    {
        return Book::class;
    }
}
