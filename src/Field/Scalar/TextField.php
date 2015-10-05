<?php
namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\SizeInterface\Implementation as SizeInterfaceImplementation;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class TextField extends Field implements SizeInterface
{
    use SizeInterfaceImplementation;
}
