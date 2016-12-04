<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractorInterface;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;

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
            $result[$value_extractor->getFieldName()] = $value_extractor->getValueCaster();
        }

        return $result;
    }

    /**
     * @var ValueExtractorInterface[]
     */
    private $value_extractors = [];

    /**
     * {@inheritdoc}
     */
    public function getValueExtractors()
    {
        return $this->value_extractors;
    }

    /**
     * {@inheritdoc}
     */
    public function &addValueExtractor(ValueExtractorInterface $extractor)
    {
        $extract_as_field = $extractor->getFieldName();

        foreach ($this->value_extractors as $value_extractor) {
            if ($value_extractor->getFieldName() === $extract_as_field) {
                throw new InvalidArgumentException("Field name '$extract_as_field' is taken");
            }
        }

        $this->value_extractors[] = $extractor;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &extractValue($extract_as_field, $expression, $default_value = null, $extractor_type = ValueExtractor::class, $is_stored = true, $is_indexed = false)
    {
        if (!empty($this->value_extractors[$extract_as_field])) {
            throw new InvalidArgumentException("Field name '$extract_as_field' is taken");
        }

        $reflection_class = new ReflectionClass($extractor_type);

        if (!$reflection_class->implementsInterface(ValueExtractorInterface::class)) {
            throw new LogicException('Extract type needs to be a class that implements \\' . ValueExtractorInterface::class . ' interface');
        }

        /** @var ValueExtractorInterface $extractor */
        $extractor = new $extractor_type($extract_as_field, $expression, $default_value);
        $extractor->storeValue($is_stored);
        $extractor->addIndex($is_indexed);

        return $this->addValueExtractor($extractor);
    }
}
