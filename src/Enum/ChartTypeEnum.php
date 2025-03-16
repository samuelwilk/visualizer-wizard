<?php
namespace App\Enum;

enum ChartTypeEnum: string
{
    case LINE = 'line';
    case BAR = 'bar';
    case PIE = 'pie';
    // Add additional chart types as needed
}
