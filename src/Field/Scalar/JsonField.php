<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Utility\JsonFieldValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\Utility\JsonFieldValueExtractorInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class JsonField extends Field implements JsonFieldInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDeserializingCode($variable_name)
    {
        return 'json_decode($' . $variable_name . ', true)';
    }

    /**
     * {@inheritdoc}
     */
    public function getCastingCode($variable_name)
    {
        return '$this->isLoading() ? $' . $variable_name . ' : json_encode($' . $variable_name . ')';
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedFields()
    {
        $result = [];

        foreach ($this->getValueExtractors() as $value_extractor) {
            $result[$value_extractor->getFieldName()] = $value_extractor->getCaster();
        }

        return $result;
    }

    /**
     * @var JsonFieldValueExtractorInterface[]
     */
    private $value_extractors = [];

    /**
     * @return JsonFieldValueExtractorInterface[]
     */
    public function getValueExtractors()
    {
        return $this->value_extractors;
    }

    /**
     * {@inheritdoc}
     */
    public function &extractValue($extract_as_field, $expression, $caster = ValueCasterInterface::CAST_STRING, $is_stored = true, $is_indexed = false)
    {
        if (!empty($this->value_extractors[$extract_as_field])) {
            throw new InvalidArgumentException("Field name '$extract_as_field' is taken");
        }

        $this->value_extractors[] = new JsonFieldValueExtractor($extract_as_field, $expression, $caster, $is_stored, $is_indexed);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &extractIndex($index_name, $expression)
    {
        return $this;
    }
}
