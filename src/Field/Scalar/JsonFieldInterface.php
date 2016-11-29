<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Field\GeneratedFieldsInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Utility\JsonFieldValueExtractorInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
interface JsonFieldInterface extends FieldInterface, GeneratedFieldsInterface
{
    /**
     * @return JsonFieldValueExtractorInterface[]
     */
    public function getValueExtractors();

    /**
     * @param  string $extract_as_field
     * @param  string $expression
     * @param  string $caster
     * @param  bool   $is_stored
     * @param  bool   $is_indexed
     * @return $this
     */
    public function &extractValue($extract_as_field, $expression, $caster = ValueCasterInterface::CAST_STRING, $is_stored = true, $is_indexed = false);
}
