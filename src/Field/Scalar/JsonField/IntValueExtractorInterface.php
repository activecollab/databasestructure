<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\JsonField;

interface IntValueExtractorInterface extends ValueExtractorInterface
{
    /**
     * Return unsigned.
     *
     * @return bool
     */
    public function isUnsigned();

    /**
     * Set unsigned column flag.
     *
     * @param bool $value
     * @return $this
     */
    public function unsigned($value = true);
}
