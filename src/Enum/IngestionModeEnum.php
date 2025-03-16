<?php
namespace App\Enum;

enum IngestionModeEnum: string
{
    case INCREMENTAL = 'incremental';
    case REPLACE = 'replace';
}
