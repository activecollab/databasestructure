<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\GeneratedInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\OnlyOneInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\UniqueInterface;
use ActiveCollab\DatabaseStructure\FieldInterface;

interface ScalarFieldInterface extends FieldInterface, GeneratedInterface, OnlyOneInterface, RequiredInterface, UniqueInterface
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
     * Get value caster for this field.
     *
     * @return string
     */
    public function getValueCaster(): string;

    /**
     * Return value casting code, that is called when value is set for a field.
     *
     * @param  string $variable_name
     * @return string
     */
    public function getCastingCode($variable_name): string;

    /**
     * Return field type definition for CREATE TABLE statement.
     */
    public function getSqlTypeDefinition(ConnectionInterface $connection): string;

    /**
     * Get field statement for SELECT query, usually just escaped field name.
     */
    public function getSqlReadStatement(): string;

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
