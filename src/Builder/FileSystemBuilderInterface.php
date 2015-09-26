<?php

namespace ActiveCollab\DatabaseStructure\Builder;

/**
 * @package ActiveCollab\DatabaseStructure\Builder
 */
interface FileSystemBuilderInterface
{
    /**
     * Return build path
     *
     * @return string
     */
    public function getBuildPath();

    /**
     * Set build path. If empty, class will be built in memory
     *
     * @param  string $value
     * @return $this
     */
    public function &setBuildPath($value);
}