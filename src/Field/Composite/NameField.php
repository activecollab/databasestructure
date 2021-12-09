<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\TypeInterface;

class NameField extends StringField
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'name', $default_value = null, $add_index = false)
    {
        parent::__construct($name, $default_value, $add_index);

        if ($default_value !== null) {
            $this->modifier('trim');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        $type->serialize($this->getName());
    }
}
