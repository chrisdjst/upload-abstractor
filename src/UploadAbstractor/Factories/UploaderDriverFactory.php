<?php

/**
 * File: src/UploadAbstractor/Factories/UploaderDriverFactory.php
 *
 * Factory class that creates UploaderInterface instances (LocalUpload ou S3Upload).
 */

namespace UploadAbstractor\Factories;

use UploadAbstractor\Enums\UploaderDriver;
use UploadAbstractor\Interfaces\UploaderInterface;
use UploadAbstractor\Drivers\LocalUpload;
use UploadAbstractor\Drivers\S3Upload;
use UploadAbstractor\Config\ArrayUploadConfig;
use UploadAbstractor\Config\EnvUploadConfig;
use EnvManager\EnvManager;

class UploaderDriverFactory
{
    /**
     * File: create
     *
     * Instantiates e retorna um driver conforme enum UploaderDriver:
     * - LOCAL: LocalUpload sem configuração adicional.
     * - S3:    S3Upload, usando EnvUploadConfig com um EnvManager.
     *
     * @param UploaderDriver                         $option Enum indicando o driver
     *                                                       desejado (S3 ou LOCAL).
     * @param ArrayUploadConfig|EnvUploadConfig|null $config Configuração opcional 
     *                                                       para o driver S3.
     * 
     * @return UploaderInterface
     *
     * @throws \InvalidArgumentException Se o enum não for suportado.
     */
    public static function create(
        UploaderDriver $option,
        ?object $config = null
    ): UploaderInterface {
        return match ($option) {
            UploaderDriver::LOCAL => new LocalUpload($config?->toArray()['base_path'] ?? null),

            UploaderDriver::S3 => self::createS3Driver($config),

            default => throw new \InvalidArgumentException("Unsupported driver option: {$option->value}"),
        };
    }

    /**
     * File: createS3Driver
     *
     * Garante que exista um UploadConfig (ArrayUploadConfig ou EnvUploadConfig).
     * Se for null, cria um EnvUploadConfig usando EnvManager padrão.
     *
     * @param ArrayUploadConfig|EnvUploadConfig|null $config Configuração de upload.
     * 
     * @return S3Upload
     */
    private static function createS3Driver(?object $config): S3Upload
    {
        // Se o usuário passou um ArrayUploadConfig, usa-o diretamente.
        if ($config instanceof ArrayUploadConfig) {
            $s3ConfigArray = $config->toArray();
            return new S3Upload($s3ConfigArray);
        }

        // Se o usuário passou um EnvUploadConfig, usa-o diretamente.
        if ($config instanceof EnvUploadConfig) {
            // Serializa para array e passa ao S3Upload
            $s3ConfigArray = $config->toArray();
            return new S3Upload($s3ConfigArray);
        }

        // Se config for null ou outro tipo, cria um EnvUploadConfig novo
        $envManager = EnvManager::createWithDefaultReaders();
        $envConfig  = new EnvUploadConfig($envManager);
        $s3ConfigArray = $envConfig->toArray();

        return new S3Upload($s3ConfigArray);
    }
}
