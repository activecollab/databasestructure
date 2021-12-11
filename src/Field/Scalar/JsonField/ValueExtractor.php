<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;

use ActiveCollab\DatabaseConnection\Record\ValueCasterInterface;
use InvalidArgumentException;

class ValueExtractor implements ValueExtractorInterface
{
    /**
     * @var string
     */
    private $field_name;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var mixed|null
     */
    private $default_value;

    /**
     * @var string
     */
    private $value_caster = ValueCasterInterface::CAST_STRING;

    /**
     * @var bool
     */
    private $store_value = false;

    /**
     * @var bool
     */
    private $add_index = false;

    /**
     * ValueExtractor constructor.
     *
     * @param string     $field_name
     * @param string     $expression
     * @param mixed|null $default_value
     */
    public function __construct($field_name, $expression, $default_value = null)
    {
        if (empty($field_name)) {
            throw new InvalidArgumentException('Field name is required');
        }

        if (empty($expression)) {
            throw new InvalidArgumentException('Value extraction expression is required');
        }

        $this->field_name = $field_name;
        $this->expression = $expression;
        $this->default_value = $default_value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueCaster()
    {
        return $this->value_caster;
    }

    /**
     * {@inheritdoc}
     */
    public function &valueCaster($value_caster)
    {
        $this->value_caster = $value_caster;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreValue()
    {
        return $this->store_value;
    }

    /**
     * {@inheritdoc}
     */
    public function &storeValue($store_value = true)
    {
        $this->store_value = (bool) $store_value;

        return $this;
    }

    public function getAddIndex(): bool
    {
        return $this->add_index;
    }

    /**
     * {@inheritdoc}
     */
    public function &addIndex($add_index = true)
    {
        $this->add_index = (bool) $add_index;

        return $this;
    }
}
