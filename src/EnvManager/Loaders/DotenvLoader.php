<?php

namespace EnvManager\Loaders;

class DotenvLoader
{
    private bool $loaded = false;

    public function load(string $path): void
    {
        if ($this->loaded) {
            return;
        }

        if (file_exists($path . '/.env') && class_exists(\Dotenv\Dotenv::class)) {
            $dotenv = \Dotenv\Dotenv::createImmutable($path);
            $dotenv->safeLoad();
        }

        $this->loaded = true;
    }
}
