<?php

use Illuminate\Support\Facades\Schedule;

Schedule::daily()->group(function () {
    Schedule::command('activitylog:clean --force');
    Schedule::command('hauling:mark-as-done');
});
