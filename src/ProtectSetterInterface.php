<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure;

/**
 * @package ActiveCollab\DatabaseStructure
 */
interface ProtectSetterInterface
{
    /**
     * @return bool
     */
    public function getProtectSetter();

    /**
     * Generate setter as protected, insted of public.
     *
     * @param  bool  $value
     * @return $this
     */
    public function &protectSetter($value = true);
}
