<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

interface RecordInterface
{
    public function getTableName(): string;

    public function getFields(): array;

    public function getValues(): array;

    public function getComment(): string;
}
