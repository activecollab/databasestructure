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
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractor;
use ActiveCollab\DatabaseStructure\Field\Scalar\JsonField\ValueExtractorInterface;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;

class JsonField extends ScalarField implements JsonFieldInterface
{
    public function getDeserializingCode($variable_name): string
    {
        return 'json_decode($' . $variable_name . ', true)';
    }

    public function getValueCaster(): string
    {
        return ValueCasterInterface::CAST_JSON;
    }

    public function getCastingCode(string $variable_name): string
    {
        return sprintf(
            '$this->isLoading() ? $%s : json_encode($%s)',
            $variable_name,
            $variable_name
        );
    }

    public function getSqlTypeDefinition(ConnectionInterface $connection): string
    {
        return 'JSON';
    }

    public function getGeneratedFields(): array
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
    private array $value_extractors = [];

    public function getValueExtractors(): array
    {
        return $this->value_extractors;
    }

    public function addValueExtractor(ValueExtractorInterface $extractor): static
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

    public function extractValue(
        string $extract_as_field,
        string $expression,
        mixed $default_value = null,
        string $extractor_type = ValueExtractor::class,
        bool $is_stored = true,
        bool $is_indexed = false
    ): static
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
