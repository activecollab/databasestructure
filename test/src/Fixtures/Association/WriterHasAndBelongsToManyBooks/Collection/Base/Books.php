<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Collection\Base;

use ActiveCollab\DatabaseObject\Collection\Type as TypeCollection;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Collection\BooksInterface;

abstract class Books extends TypeCollection implements BooksInterface
{
    /**
     * Return type that this collection works with.
     *
     * @return string
     */
    public function getType()
    {
        return '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasAndBelongsToManyBooks\\Book';
    }
}
