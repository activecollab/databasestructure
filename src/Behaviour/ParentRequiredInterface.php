<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;

interface ParentRequiredInterface
{
    public function getParent(bool $use_cache = true): EntityInterface;

    public function &setParent(EntityInterface $value): ParentInterface;

    public function getParentType(): string;

    public function getParentId(): int;
}
