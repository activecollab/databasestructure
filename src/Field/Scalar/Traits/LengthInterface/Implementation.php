<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits\LengthInterface;

use InvalidArgumentException;

trait Implementation
{
    /**
     * @var string
     */
    private $length = 191;

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function &length($length)
    {
        $length = (int) $length;

        if ($length < $this->getMinLength()) {
            throw new InvalidArgumentException("Min length is {$this->getMinLength()}");
        }

        if ($length > $this->getMaxLength()) {
            throw new InvalidArgumentException("Max length is {$this->getMaxLength()}");
        }

        $this->length = $length;

        return $this;
    }

    /**
     * @return int
     */
    protected function getMinLength()
    {
        return 1;
    }

    /**
     * @return int
     */
    protected function getMaxLength()
    {
        return 191;
    }
}
