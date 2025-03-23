<?php

namespace UploadAbstractor\Support\Readers;

use UploadAbstractor\Support\Contracts\EnvironmentInterface;

class ServerEnvReader implements EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}
