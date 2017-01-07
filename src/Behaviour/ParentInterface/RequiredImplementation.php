<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\ParentInterface;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ParentInterface;
use LogicException;

/**
 * @property \ActiveCollab\DatabaseObject\PoolInterface $pool
 */
trait RequiredImplementation
{
    public function getParent(bool $use_cache = true): EntityInterface
    {
        if ($id = $this->getParentId()) {
            return $this->pool->getById($this->getParentType(), $id, $use_cache);
        } else {
            return null;
        }
    }

    /**
     * @param  EntityInterface      $value
     * @return ParentInterface|$this
     */
    public function &setParent(EntityInterface $value): ParentInterface
    {
        if (!$value->isLoaded()) {
            throw new LogicException('Parent needs to be saved to the database.');
        }

        $this->setParentType(get_class($value));
        $this->setParentId($value->getId());

        return $this;
    }

    abstract public function getParentType(): string;

    abstract public function &setParentType(string $value);

    abstract public function getParentId(): int;

    abstract public function &setParentId(int $value);
}
