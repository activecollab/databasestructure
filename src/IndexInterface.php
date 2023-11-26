<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

interface IndexInterface
{
    const INDEX = 'INDEX';
    const PRIMARY = 'PRIMARY';
    const UNIQUE = 'UNIQUE';
    const FULLTEXT = 'FULLTEXT';
    const SPATIAL = 'SPATIAL';

    const INDEX_TYPES = [
        self::INDEX,
        self::PRIMARY,
        self::UNIQUE,
        self::FULLTEXT,
        self::SPATIAL,
    ];

    public function getName(): string;
    public function getFields(): array;
    public function getIndexType(): string;
}
