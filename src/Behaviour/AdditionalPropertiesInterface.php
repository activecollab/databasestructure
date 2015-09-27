<?php

namespace ActiveCollab\DatabaseStructure\Behaviour;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour
 */
interface AdditionalPropertiesInterface
{
    /**
     * Return additional log properties as array
     *
     * @return array
     */
    public function getAdditionalProperties();

    /**
     * Set attributes value
     *
     * @param  array|null $value
     * @return $this
     */
    public function &setAdditionalProperties(array $value = null);

    /**
     * Returna attribute value
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getAdditionalProperty($name, $default = null);

    /**
     * Set attribute value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function &setAdditionalProperty($name, $value);
}