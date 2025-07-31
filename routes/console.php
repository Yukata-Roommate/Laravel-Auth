<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command("token:delete")->everyFifteenMinutes();
