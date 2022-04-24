<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Builder;

use ParseError;

abstract class FileSystemBuilder extends Builder implements FileSystemBuilderInterface
{
    /**
     * Build path. If empty, class will be built to memory.
     *
     * @var string
     */
    private $build_path;

    /**
     * Return build path.
     */
    public function getBuildPath(): ?string
    {
        return $this->build_path;
    }

    public function setBuildPath(?string $value): FileSystemBuilderInterface
    {
        $this->build_path = $value;

        return $this;
    }

    protected function openPhpFile(): array
    {
        $result = [
            '<?php',
            '',
        ];

        if ($this->getStructure()->getConfig('header_comment')) {
            $result = array_merge(
                $result,
                explode("\n", $this->getStructure()->getConfig('header_comment'))
            );
            $result[] = '';
        }

        $result[] = 'declare(strict_types=1);';
        $result[] = '';

        return $result;
    }

    protected function writeOrEval(?string $path, array $result): ?string
    {
        $result = implode("\n", $result);

        if ($this->getBuildPath()) {
            file_put_contents($path, $result);
        } else {
            try {
                eval(ltrim($result, '<?php'));
            } catch (ParseError $e) {
                var_dump($result);
                throw $e;
            }
        }

        return $path;
    }
}
