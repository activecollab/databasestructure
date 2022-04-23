<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;

class DecimalField extends NumberField
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

    public function getNativeType(): string
    {
        return 'float';
    }

    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_FLOAT;
    }

    public function getCastingCode($variable_name): string
    {
        return '(float) $' . $variable_name;
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        $result = sprintf('DECIMAL(%d, %d)', $this->getLength(), $this->getScale());

        if ($this->isUnsigned()) {
            $result .= ' UNSIGNED';
        }

        return $result;
    }
}
