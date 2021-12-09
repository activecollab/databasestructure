<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\CreatedByInterface;

trait Implementation
{
    public function ActiveCollabDatabaseStructureBehaviourCreatedByInterfaceImplementation()
    {
        $this->registerEventHandler('on_before_save', function () {
            if (empty($this->getFieldValue('created_by_id'))) {
                $resolve_created_by_id = $this->resolveCreatedById();

                if ($resolve_created_by_id) {
                    $this->setFieldValue('created_by_id', $this->resolveCreatedById());
                }
            }
        });
    }

    /**
     * Register an internal event handler.
     *
     * @param string   $event
     * @param callable $handler
     */
    abstract protected function registerEventHandler($event, callable $handler);

    abstract public function getFieldValue($field, $default = null);

    abstract public function &setFieldValue($field, $value);

    abstract protected function resolveCreatedById(): ?int;
}
