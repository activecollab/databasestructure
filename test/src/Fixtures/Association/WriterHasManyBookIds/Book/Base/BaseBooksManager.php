<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Base;

use ActiveCollab\DatabaseObject\Entity\Manager;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Book\Book;

abstract class BaseBooksManager extends Manager
{
    /**
     * Return type that this manager works with.
     *
     * @return string
     */
    public function getType()
    {
        return Book::class;
    }
}
