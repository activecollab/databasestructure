<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

use ActiveCollab\DatabaseStructure\FieldInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Traits
 */
trait Size
{
    /**
     * @var string
     */
    private $size = FieldInterface::SIZE_NORMAL;

    /**
     * Return size of the field, if set
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param  string $size
     * @return $this
     */
    public function &size($size)
    {
        if (in_array($size, $this->getSupportedSizes())) {
            $this->size = $size;
        } else {
            throw new InvalidArgumentException("Size '$size' is not supported");
        }

        return $this;
    }

    /**
     * Return an array of supported sizes
     *
     * @return array
     */
    protected function getSupportedSizes()
    {
        return [FieldInterface::SIZE_TINY, FieldInterface::SIZE_SMALL, FieldInterface::SIZE_MEDIUM, FieldInterface::SIZE_NORMAL, FieldInterface::SIZE_BIG];
    }
}