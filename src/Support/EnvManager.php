<?php

namespace UploadAbstractor\Support;

use UploadAbstractor\Support\Contracts\EnvironmentInterface;
use UploadAbstractor\Support\Loaders\DotenvLoader;
use UploadAbstractor\Support\Readers\{ArrayEnvReader, ServerEnvReader, GetenvReader};

class EnvManager
{
    /** @var EnvironmentInterface[] */
    private array $readers = [];

    public function __construct(
        private readonly DotenvLoader $loader,
        string $rootPath
    ) {
        $this->loader->load($rootPath);
    }

    public function addReader(EnvironmentInterface $reader): void
    {
        $this->readers[] = $reader;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        foreach ($this->readers as $reader) {
            $value = $reader->get($key, null);
            if ($value !== null) {
                return $value;
            }
        }

        return $default;
    }

    public static function createWithDefaultReaders(): self
    {
        $instance = new self(new DotenvLoader(), dirname(__DIR__, 2));
        $instance->addReader(new ArrayEnvReader());
        $instance->addReader(new ServerEnvReader());
        $instance->addReader(new GetenvReader());
        return $instance;
    }
}
