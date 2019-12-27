<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer\Base;

use ActiveCollab\DatabaseObject\Collection\Type as TypeCollection;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer\Writer;

abstract class WritersManager extends TypeCollection
{
    /**
     * Return type that this collection works with.
     *
     * @return string
     */
    public function getType()
    {
        return Writer::class;
    }
}
