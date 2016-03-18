<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Field\Scalar\StringField as ScalarStringField;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface;
use ActiveCollab\DatabaseStructure\Field\Scalar\Traits\RequiredInterface\Implementation as RequiredInterfaceImplementation;
use ActiveCollab\DatabaseStructure\FieldInterface;
use ActiveCollab\DatabaseStructure\Index;
use ActiveCollab\DatabaseStructure\TypeInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class AddressField extends Field implements RequiredInterface
{
    use RequiredInterfaceImplementation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $field_name_prefix = '';

    /**
     * @var bool
     */
    private $index_on_city;

    /**
     * @var bool
     */
    private $index_on_zip_code;

    /**
     * @var bool
     */
    private $index_on_region;

    /**
     * @var bool
     */
    private $index_on_country;

    /**
     * @param string $name
     * @param bool   $index_on_city
     * @param bool   $index_on_zip_code
     * @param bool   $index_on_region
     * @param bool   $index_on_country
     */
    public function __construct($name = 'address', $index_on_city = false, $index_on_zip_code = false, $index_on_region = false, $index_on_country = false)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Value '$name' is not a valid field name");
        }

        if ($name === 'address') {
            $this->name = $name;
        } else {
            if (substr($name, -8) == '_address') {
                $this->name = $name;
                $this->field_name_prefix = substr($name, 0, strlen($name) - 8);
            } else {
                throw new InvalidArgumentException("Name of the address field should be 'address' or end with '_address'");
            }
        }

        $this->index_on_city = $index_on_city;
        $this->index_on_zip_code = $index_on_zip_code;
        $this->index_on_region = $index_on_region;
        $this->index_on_country = $index_on_country;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getIndexOnCity()
    {
        return $this->index_on_city;
    }

    /**
     * @return bool
     */
    public function getIndexOnZipCode()
    {
        return $this->index_on_zip_code;
    }

    /**
     * @return bool
     */
    public function getIndexOnRegion()
    {
        return $this->index_on_region;
    }

    /**
     * @return bool
     */
    public function getIndexOnCountry()
    {
        return $this->index_on_country;
    }

    /**
     * @return string
     */
    public function getFieldNamePrefix()
    {
        return $this->field_name_prefix;
    }

    /**
     * Return string name with a prefix.
     *
     * @param  string $field_name
     * @return string
     */
    private function getPrefixedFieldName($field_name)
    {
        return $this->field_name_prefix ? "{$this->field_name_prefix}_{$field_name}" : $field_name;
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return [
            new ScalarStringField($this->getName()),
            new ScalarStringField($this->getPrefixedFieldName('address_extended')),
            new ScalarStringField($this->getPrefixedFieldName('city')),
            new ScalarStringField($this->getPrefixedFieldName('zip_code')),
            new ScalarStringField($this->getPrefixedFieldName('region')),
            (new CountryCodeField($this->getPrefixedFieldName('country_code')))->required(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        if ($this->getIndexOnCity()) {
            $type->addIndex(new Index($this->getPrefixedFieldName('city')));
        }

        if ($this->getIndexOnZipCode()) {
            $type->addIndex(new Index($this->getPrefixedFieldName('zip_code')));
        }

        if ($this->getIndexOnRegion()) {
            $type->addIndex(new Index($this->getPrefixedFieldName('region')));
        }

        if ($this->getIndexOnCountry()) {
            $type->addIndex(new Index($this->getPrefixedFieldName('country')));
        }
    }
}
