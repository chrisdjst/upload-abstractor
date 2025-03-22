<?php
namespace App\Upload\Enums;

enum UploadDriver: string
{
    case S3 = 's3';
    case LOCAL = 'local';
}
