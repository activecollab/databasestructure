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
class CountryCodeField extends StringField
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'country_code', $default_value = null, $add_index = false)
    {
        parent::__construct($name, $default_value, $add_index);

        if ($default_value !== null) {
            $this->modifier('trim');
        }

        $this->length(2);
    }
}
