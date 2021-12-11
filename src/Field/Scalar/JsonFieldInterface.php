<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\GeneratedFieldsInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractorInterface;

interface JsonFieldInterface extends ScalarFieldInterface, GeneratedFieldsInterface
{
    /**
     * @return ValueExtractorInterface[]
     */
    public function getValueExtractors(): array;
    public function addValueExtractor(ValueExtractorInterface $extractor): static;
    public function extractValue(
        string $extract_as_field,
        string $expression,
        mixed $default_value = null,
        string $extractor_type = ValueExtractor::class,
        bool $is_stored = true,
        bool $is_indexed = false
    ): static;
}
