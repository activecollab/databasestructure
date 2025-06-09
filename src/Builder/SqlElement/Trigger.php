<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Builder\SqlElement;

class Trigger extends SqlElement
{
    public function __construct(string $table_name, string $trigger_name)
    {
        parent::__construct(sprintf('%s-%s', $table_name, $trigger_name));
    }
}
