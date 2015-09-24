<?php

namespace ActiveCollab\DatabaseStructure\Field\Scalar;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Scalar
 */
class MoneyField extends DecimalField
{
    /**
     * @param string $name
     * @param float  $default_value
     */
    public function __construct($name, $default_value = null)
    {
        parent::__construct($name, $default_value);

        $this->length(13);
        $this->scale(3);
    }
}