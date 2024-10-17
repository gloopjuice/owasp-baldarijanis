<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChangeTrackingService
{
    protected $logFile;

    public function __construct()
    {
        $this->logFile = storage_path('logs/change-tracking.log');
    }

    public function logChange($message)
    {
        file_put_contents($this->logFile, $message . PHP_EOL, FILE_APPEND);
    }
}
