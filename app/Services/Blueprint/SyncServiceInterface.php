<?php

namespace App\Services\Blueprint;

interface SyncServiceInterface
{
    public function start(array $settings);
    public function reset(array $modules);
}
