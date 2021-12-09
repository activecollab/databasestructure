<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;

interface ValueExtractorInterface
{
    /**
     * @return string
     */
    public function getFieldName();

    /**
     * @return string
     */
    public function getExpression();

    /**
     * @return mixed|null
     */
    public function getDefaultValue();

    /**
     * @return string
     */
    public function getValueCaster();

    /**
     * Set value caster.
     *
     * @param  string $value_caster
     * @return $this
     */
    public function valueCaster($value_caster);

    /**
     * @return bool
     */
    public function getStoreValue();

    /**
     * @param  bool  $store_value
     * @return $this
     */
    public function storeValue($store_value = true);

    /**
     * @return bool
     */
    public function getAddIndex();

    /**
     * @param  bool  $add_index
     * @return $this
     */
    public function addIndex($add_index = true);
}
