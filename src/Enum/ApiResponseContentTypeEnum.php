<?php
namespace App\Enum;

enum ApiResponseContentTypeEnum: string
{
    case JSON = 'application/json';
    case CSV = 'text/csv';
    case XML = 'application/xml';
}
