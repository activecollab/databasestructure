<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Manager\Base;

use ActiveCollab\DatabaseObject\Entity\Manager;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Manager\BooksInterface;

abstract class Books extends Manager implements BooksInterface
{
    public function getType(): string
    {
        return '\\ActiveCollab\\DatabaseStructure\\Test\\Fixtures\\Association\\WriterHasAndBelongsToManyBooks\\Book';
    }
}
