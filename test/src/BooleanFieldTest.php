<?php

namespace ActiveCollab\DatabaseStructure\Test;

use ActiveCollab\DatabaseStructure\Field\Scalar\BooleanField;

/**
 * @package ActiveCollab\DatabaseStructure\Test
 */
class BooleanFieldTest extends TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testExceptionWhenBooleanFieldIsUnique()
    {
        (new BooleanField('should_not_be_required'))->unique();
    }
}