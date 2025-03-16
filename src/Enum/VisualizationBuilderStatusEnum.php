<?php

namespace App\Enum;

enum VisualizationBuilderStatusEnum: string
{
    case IN_PROGRESS = 'in_progress';
    case WAITING_FOR_REVIEW = 'waiting_for_review';
    case FINALIZED = 'finalized';
    case EDITING_EXISTING = 'editing_existing';
    case DUPLICATING = 'duplicating';
}
