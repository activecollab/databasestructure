<?php
namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use LogicException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class Boolean extends Field
{
    /**
     * Value of this column needs to be unique (in the given context)
     *
     * @param  array|string|null $context
     * @return $this
     */
    public function &unique($context = null)
    {
        throw new LogicException('Boolean columns cant be made unique');
    }
}