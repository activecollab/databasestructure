<?php

namespace ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface
 */
trait Implementation
{
    /**
     * Cached log attributes value
     *
     * @var array
     */
    private $decoded_additional_properties = false;

    /**
     * Return additional log properties as array
     *
     * @return array
     */
    public function getAdditionalProperties()
    {
        if ($this->decoded_additional_properties === false) {
            $raw = trim($this->getRawAdditionalProperties());
            $this->decoded_additional_properties = empty($raw) ? [] : json_decode($raw, true);

            if (!is_array($this->decoded_additional_properties)) {
                $this->decoded_additional_properties = [];
            }
        }

        return $this->decoded_additional_properties;
    }

    /**
     * Set attributes value
     *
     * @param  array|null $value
     * @return $this
     */
    public function &setAdditionalProperties(array $value = null)
    {
        $this->decoded_additional_properties = false; // Reset...

        $this->setRawAdditionalProperties(json_encode($value));

        return $this;
    }

    /**
     * Returna attribute value
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getAdditionalProperty($name, $default = null)
    {
        $additional_properties = $this->getAdditionalProperties();

        return $additional_properties && isset($additional_properties[$name]) ? $additional_properties[$name] : $default;
    }

    /**
     * Set attribute value
     *
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     */
    public function &setAdditionalProperty($name, $value)
    {
        $additional_properties = $this->getAdditionalProperties();

        if ($value === null) {
            if (isset($additional_properties[$name])) {
                unset($additional_properties[$name]);
            }
        } else {
            $additional_properties[$name] = $value;
        }

        $this->setAdditionalProperties($additional_properties);

        return $this;
    }

    // ---------------------------------------------------
    //  Expectations
    // ---------------------------------------------------

    /**
     * Get raw additional properties value
     *
     * @return string
     */
    abstract public function getRawAdditionalProperties();

    /**
     * Set raw additional properties value
     *
     * @param  string $value
     * @return $this
     */
    abstract public function &setRawAdditionalProperties($value);
}