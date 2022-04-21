<?php

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Test\Fixtures\Association\WriterHasAndBelongsToManyBooks\Base;

interface WriterInterface extends \ActiveCollab\DatabaseStructure\Entity\EntityInterface
{
    public function getName(): string;

    /**
     * Set value of name field.
     *
     * @param  string $value
     * @return $this
     */
    public function setName(string $value);
}
