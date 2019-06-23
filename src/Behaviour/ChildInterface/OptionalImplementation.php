<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\ChildInterface;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ChildInterface;
use LogicException;

/**
 * @property \ActiveCollab\DatabaseObject\PoolInterface $pool
 */
trait OptionalImplementation
{
    public function getParent(bool $use_cache = true): ?EntityInterface
    {
        if ($id = $this->getParentId()) {
            return $this->pool->getById($this->getParentType(), $id, $use_cache);
        } else {
            return null;
        }
    }

    /**
     * @param  EntityInterface|null $value
     * @return ChildInterface|$this
     */
    public function &setParent(?EntityInterface $value): ChildInterface
    {
        if ($value === null) {
            $this->setParentType(null);
            $this->setParentId(null);
        } else {
            if (!$value->isLoaded()) {
                throw new LogicException('Parent needs to be saved to the database.');
            }

            $this->setParentType(get_class($value));
            $this->setParentId($value->getId());
        }

        return $this;
    }

    abstract public function getParentType(): ?string;

    abstract public function &setParentType(?string $value);

    abstract public function getParentId(): ?int;

    abstract public function &setParentId(?int $value);
}
