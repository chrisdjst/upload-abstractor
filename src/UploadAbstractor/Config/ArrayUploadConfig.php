<?php

namespace UploadAbstractor\Config;

class ArrayUploadConfig implements UploadConfigInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function toArray(): array
    {
        return $this->config;
    }
}
