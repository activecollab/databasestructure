<?php

namespace ActiveCollab\DatabaseStructure\Test\CompositeFields;

use ActiveCollab\DatabaseStructure\Field\Composite\CreatedByField;
use ActiveCollab\DatabaseStructure\Test\TestCase;

/**
 * @package ActiveCollab\DatabaseStructure\Test\CompositeFields
 */
class CreatedByFieldTest extends TestCase
{
    /**
     * Test default name
     */
    public function testDefaultName()
    {
        $this->assertEquals('created_by_id', (new CreatedByField('User'))->getName());
    }
}
