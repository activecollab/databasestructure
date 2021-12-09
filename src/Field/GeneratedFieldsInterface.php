<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field;

interface GeneratedFieldsInterface
{
    /**
     * Return an array of generated fields that parent field adds to the type.
     *
     * @return array
     */
    public function getGeneratedFields();
}
