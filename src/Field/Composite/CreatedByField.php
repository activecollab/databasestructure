<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\Behaviour\CreatedByOptionalInterface;
use ActiveCollab\DatabaseStructure\Behaviour\CreatedByRequiredInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\User\AnonymousUser;

/**
 * @package ActiveCollab\DatabaseStructure\Field\Composite
 */
class CreatedByField extends ActionByField
{
    /**
     * @param string $user_class_name
     * @param string $anonymous_user_class_name
     * @param bool   $add_index
     */
    public function __construct($user_class_name, $anonymous_user_class_name = AnonymousUser::class, $add_index = true)
    {
        parent::__construct('created_by_id', $user_class_name, $anonymous_user_class_name, $add_index);
    }

    /**
     * {@inheritdoc}
     */
    public function onAddedToType(TypeInterface &$type)
    {
        parent::onAddedToType($type);

        if ($this->isRequired()) {
            $type->addTrait(CreatedByRequiredInterface::class);
        } else {
            $type->addTrait(CreatedByOptionalInterface::class);
        }
    }
}
