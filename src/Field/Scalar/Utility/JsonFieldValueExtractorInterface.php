<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Utility;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar\Utility
 */
interface JsonFieldValueExtractorInterface
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
     * @return string
     */
    public function getCaster();

    /**
     * @return bool
     */
    public function isStored();

    /**
     * @return bool
     */
    public function isIndexed();
}
