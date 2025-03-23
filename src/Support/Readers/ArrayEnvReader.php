<?php

namespace UploadAbstractor\Support\Readers;

use UploadAbstractor\Support\Contracts\EnvironmentInterface;

class ArrayEnvReader implements EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}
