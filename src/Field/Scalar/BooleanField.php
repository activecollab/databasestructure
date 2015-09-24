<?php
namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use LogicException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class BooleanField extends Field
{
    /**
     * Value of this column needs to be unique (in the given context)
     *
     * @param  string $context
     * @return $this
     */
    public function &unique(...$context)
    {
        throw new LogicException('Boolean columns cant be made unique');
    }
}