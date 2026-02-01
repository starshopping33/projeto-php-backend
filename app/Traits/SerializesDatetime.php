<?php

namespace App\Traits;

use DateTimeInterface;

trait SerializesDatetime
{
    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i:s');
    }
}