<?php

declare(strict_types=1);

namespace App\Enum;

enum TrainingModeEnum: string
{
    case TRAINING = 'training';
    case EXAMEN = 'examen';
}
