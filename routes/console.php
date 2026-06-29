<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:scan-follow-ups')->weeklyOn(0, '2:00');
