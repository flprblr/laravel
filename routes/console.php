<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->daily();

Schedule::command('streetmachine:process')
    ->everyFiveMinutes()
    ->withoutOverlapping();
