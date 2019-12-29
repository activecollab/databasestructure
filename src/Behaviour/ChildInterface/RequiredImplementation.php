<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Behaviour\ChildInterface;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\DatabaseStructure\Behaviour\ChildInterface;
use LogicException;

trait RequiredImplementation
{
    public function getParent(bool $use_cache = true): EntityInterface
    {
        if ($id = $this->getParentId()) {
            return $this->getPool()->getById($this->getParentType(), $id, $use_cache);
        } else {
            return null;
        }
    }

    /**
     * @param  EntityInterface      $value
     * @return ChildInterface|$this
     */
    public function &setParent(EntityInterface $value): ChildInterface
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

    abstract protected function getPool(): PoolInterface;
}
