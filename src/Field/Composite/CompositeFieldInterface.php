<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Field\Composite;

use ActiveCollab\DatabaseStructure\FieldInterface;

interface CompositeFieldInterface extends FieldInterface
{
    /**
     * Return fields that this field is composed of.
     *
     * @return FieldInterface[]
     */
    public function getFields(): array;

    /**
     * Return methods that this field needs to inject in base class.
     */
    public function getBaseClassMethods(string $indent, array &$result): void;

    /**
     * Return methods that this field needs to inject in base class.
     */
    public function getBaseInterfaceMethods(string $indent, array &$result): void;

    /**
     * @param string $indent
     * @param array  $result
     */
    public function getValidatorLines($indent, array &$result);
}
