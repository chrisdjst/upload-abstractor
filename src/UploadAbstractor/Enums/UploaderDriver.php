<?php
namespace UploadAbstractor\Enums;

enum UploaderDriver: string
{
    case S3 = 's3';
    case LOCAL = 'local';
}
