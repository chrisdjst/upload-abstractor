<?php
namespace UploadAbstractor\Enums;

enum UploadDriver: string
{
    case S3 = 's3';
    case LOCAL = 'local';
}
