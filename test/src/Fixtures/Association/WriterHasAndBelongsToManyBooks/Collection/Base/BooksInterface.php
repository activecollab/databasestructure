<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Collection\Base;

use ActiveCollab\DatabaseObject\CollectionInterface;

interface BooksInterface extends CollectionInterface
{
    /**
     * Return type that this collection works with.
     *
     * @return string
     */
    public function getType();
}
