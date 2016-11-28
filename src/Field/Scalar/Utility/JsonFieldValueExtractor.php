<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Utility;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Utility
 */
class JsonFieldValueExtractor implements JsonFieldValueExtractorInterface
{
    /**
     * @var string
     */
    private $field_name;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var string
     */
    private $caster;

    /**
     * @var bool
     */
    private $is_stored;

    /**
     * @var bool
     */
    private $is_indexed;

    /**
     * JsonFieldValueExtractor constructor.
     *
     * @param string $field_name
     * @param string $expression
     * @param string $caster
     * @param bool   $is_stored
     * @param bool   $is_indexed
     */
    public function __construct($field_name, $expression, $caster = ValueCasterInterface::CAST_STRING, $is_stored = true, $is_indexed = false)
    {
        $this->field_name = $field_name;
        $this->expression = $expression;
        $this->caster = $caster;
        $this->is_stored = (bool) $is_stored;
        $this->is_indexed = (bool) $is_indexed;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return string
     */
    public function getCaster()
    {
        return $this->caster;
    }

    /**
     * @return bool
     */
    public function isStored()
    {
        return $this->is_stored;
    }

    /**
     * @return bool
     */
    public function isIndexed()
    {
        return $this->is_indexed;
    }
}
