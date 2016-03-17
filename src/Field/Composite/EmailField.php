<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class EmailField extends StringField
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name, $default_value = null, $add_index = false)
    {
        parent::__construct($name, $default_value, $add_index);

        if ($default_value !== null) {
            $this->modifier('trim');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatorLines($indent, array &$result)
    {
        $result[] = $indent . '$validator->email(' . var_export($this->getName(), true) . ', ' . ($this->getDefaultValue() === null ? 'true' : 'false') . ');';
    }
}
