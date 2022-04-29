<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseStructure;

interface FieldInterface extends ProtectSetterInterface
{
    const SIZE_TINY = 'tiny';
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_NORMAL = 'normal';
    const SIZE_BIG = 'big';

    public function getName(): string;
    public function onAddedToType(TypeInterface $type): void;
}
