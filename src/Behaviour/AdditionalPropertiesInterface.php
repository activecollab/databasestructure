<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface AdditionalPropertiesInterface
{
    /**
     * Return additional log properties as array.
     *
     * @return array
     */
    public function getAdditionalProperties(): array;

    /**
     * Set attributes value.
     *
     * @param  array|null                          $value
     * @return $this|AdditionalPropertiesInterface
     */
    public function &setAdditionalProperties(array $value = null): AdditionalPropertiesInterface;

    /**
     * Returna attribute value.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getAdditionalProperty(string $name, $default = null);

    /**
     * Set attribute value.
     *
     * @param  string                              $name
     * @param  mixed                               $value
     * @return $this|AdditionalPropertiesInterface
     */
    public function &setAdditionalProperty(string $name, $value): AdditionalPropertiesInterface;
}
