<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\GeneratedFieldsInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractorInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
interface JsonFieldInterface extends FieldInterface, GeneratedFieldsInterface
{
    /**
     * @return ValueExtractorInterface[]
     */
    public function getValueExtractors();

    /**
     * Add value.
     *
     * @param  ValueExtractorInterface $extractor
     * @return $this
     */
    public function &extract(ValueExtractorInterface $extractor);

    /**
     * Shortcut method that builds extractor instance from the (long) list of arguments.
     *
     * @param  string     $extract_as_field
     * @param  string     $expression
     * @param  mixed|null $default_value
     * @param  string     $extractor_type
     * @param  bool       $is_stored
     * @param  bool       $is_indexed
     * @return $this
     */
    public function &extractValue($extract_as_field, $expression, $default_value = null, $extractor_type = ValueExtractor::class, $is_stored = true, $is_indexed = false);
}
