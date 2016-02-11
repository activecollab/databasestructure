<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\FieldInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
interface InjectFieldsInsterface
{
    /**
     * Return a list of fields that are to be added to the source type.
     *
     * @return FieldInterface[]
     */
    public function getFields();
}
