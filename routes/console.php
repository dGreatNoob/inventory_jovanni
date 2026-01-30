<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Apply due pending product prices (effective date <= today) daily as a backstop
Schedule::command('products:apply-pending-prices')->dailyAt('00:05');
