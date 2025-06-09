<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\SqlElement;

class ForeignKey extends SqlElement
{
    public function __construct(string $table_name, string $key_name)
    {
        parent::__construct(sprintf('%s-%s', $table_name, $key_name));
    }
}
