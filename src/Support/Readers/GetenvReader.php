<?php

namespace UploadAbstractor\Support\Readers;

use UploadAbstractor\Support\Contracts\EnvironmentInterface;

class GetenvReader implements EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
