<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;

class FloatField extends NumberField
{
    /**
     * @var int
     */
    private $length = 12;

    /**
     * @var int
     */
    private $scale = 2;

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param  int   $value
     * @return $this
     */
    public function &length($value)
    {
        $this->length = (int) $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param  int   $value
     * @return $this
     */
    public function &scale($value)
    {
        $this->scale = (int) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeType(): string
    {
        return 'float';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_FLOAT;
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name): string
    {
        return '(float) $' . $variable_name;
    }
}
