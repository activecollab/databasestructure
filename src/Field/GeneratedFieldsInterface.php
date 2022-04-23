<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field;

interface GeneratedFieldsInterface
{
    /**
     * Return an array of generated fields that parent field adds to the type.
     */
    public function getGeneratedFields(): array;
}
