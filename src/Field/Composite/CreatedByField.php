<?php

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\User\AnonymousUser;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class CreatedByField extends ActionByField
{
    /**
     * @param string  $user_class_name
     * @param string  $anonymous_user_class_name
     * @param boolean $add_index
     */
    public function __construct($user_class_name, $anonymous_user_class_name = AnonymousUser::class, $add_index = true)
    {
        parent::__construct('created_by_id', $user_class_name, $anonymous_user_class_name, $add_index);
    }
}
