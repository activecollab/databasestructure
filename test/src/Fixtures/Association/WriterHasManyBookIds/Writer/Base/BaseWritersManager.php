<?php

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer\Base;

use ActiveCollab\DatabaseObject\Entity\Manager;
use ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasManyBookIds\Writer\Writer;

abstract class BaseWritersManager extends Manager
{
    /**
     * Return type that this manager works with.
     *
     * @return string
     */
    public function getType()
    {
        return Writer::class;
    }
}
