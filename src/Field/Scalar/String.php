<?php
namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\Modifier;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class String extends Field
{
    use Modifier;

    /**
     * Return PHP native type
     *
     * @return string
     */
    public function getNativeType()
    {
        return 'string';
    }
}