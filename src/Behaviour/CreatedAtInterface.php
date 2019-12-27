<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\DateValue\DateTimeValueInterface;
use ActiveCollab\Object\ObjectInterface;

interface CreatedAtInterface extends ObjectInterface
{
    public function getCreatedAt(): DateTimeValueInterface;
    public function &setCreatedAt(DateTimeValueInterface $value);
}
