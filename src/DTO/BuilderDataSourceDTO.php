<?php

namespace App\DTO;

use App\Entity\DataSource;

class BuilderDataSourceDTO
{
    public ?DataSource $dataSource = null;
    public ?array $selectedColumns = [];
    public ?array $transformedData = [];
}
