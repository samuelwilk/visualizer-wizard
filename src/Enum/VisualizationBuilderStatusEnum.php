<?php

namespace App\Enum;

enum VisualizationBuilderStatusEnum: string
{
    case SELECTING_DATA_SOURCE = 'selecting_data_source';
    case CONFIGURING_COLUMNS = 'configuring_columns';
    case CONFIGURING_TABLES = 'configuring_tables';
    case CONFIGURING_CHARTS = 'configuring_charts';
    case CONFIGURING_LAYOUT = 'configuring_layout';
    case WAITING_FOR_REVIEW = 'waiting_for_review';
    case FINALIZED = 'finalized';
    case EDITING_EXISTING = 'editing_existing';
    case DUPLICATING = 'duplicating';
}
