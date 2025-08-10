<?php

use Illuminate\Support\Facades\Schedule;

Schedule::daily()->group(function () {
    Schedule::command('activitylog:clean');
    Schedule::command('hauling:mark-as-done');
});
