<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

interface IndexInterface
{
    /**
     * Index types.
     */
    const INDEX = 'INDEX';
    const PRIMARY = 'PRIMARY';
    const UNIQUE = 'UNIQUE';
    const FULLTEXT = 'FULLTEXT';

    public function getName(): string;

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return string
     */
    public function getIndexType();
}
