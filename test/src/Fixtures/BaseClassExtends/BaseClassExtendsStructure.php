<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\BaseClassExtends;

use ActiveCollab\DatabaseStructure\Structure;

class BaseClassExtendsStructure extends Structure
{
    public function __construct(string $base_class_extends = null)
    {
        if ($base_class_extends) {
            $this->setConfig('base_class_extends', $base_class_extends);
        }

        parent::__construct();
    }

    
    public function configure(): void
    {
        $this->addType('writers');
    }
}
