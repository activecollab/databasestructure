<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure\Field\Scalar\Traits;

interface ModifierInterface extends FieldTraitInterface
{
    /**
     * Return name of the modifier, if set.
     *
     * @return string
     */
    public function getModifier();

    /**
     * @param  string $modifier
     * @return $this
     */
    public function &modifier($modifier);
}
