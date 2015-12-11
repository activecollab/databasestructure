<?php

namespace ActiveCollab\DatabaseStructure\Association;

use ActiveCollab\DatabaseStructure\AssociationInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseStructure\Association
 */
class HasManyViaAssociation extends HasManyAssociation implements AssociationInterface
{
    /**
     * @var string
     */
    private $intermediary_type_name;

    /**
     * @param string $name
     * @param string $intermediary_type_name
     * @param string $target_type_name
     */
    public function __construct($name, $intermediary_type_name, $target_type_name = null)
    {
        parent::__construct($name, $target_type_name);

        if (empty($intermediary_type_name)) {
            throw new InvalidArgumentException("Value '$intermediary_type_name' is not a valid type name");
        }

        $this->intermediary_type_name = $intermediary_type_name;
    }
}
