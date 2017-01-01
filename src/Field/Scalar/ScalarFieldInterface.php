<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseStructure\FieldInterface;

interface ScalarFieldInterface extends FieldInterface
{
    /**
     * Return PHP native type.
     *
     * @return string
     */
    public function getNativeType(): string;

    /**
     * Return de-serialized value, on get field value.
     *
     * This method should be unsed only for fields that store serialized data, like JSON or serialized PHP values.
     *
     * @param  string $variable_name
     * @return string
     */
    public function getDeserializingCode($variable_name): string;

    /**
     * Return value casting code, that is called when value is set for a field.
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name): string;

    /**
     * Return true if this field should be part of the model, or does it do its work in background.
     *
     * @return bool
     */
    public function getShouldBeAddedToModel(): bool;

    /**
     * @param  bool                 $value
     * @return ScalarFieldInterface
     */
    public function &setShouldBeAddedToModel(bool $value): ScalarFieldInterface;
}
